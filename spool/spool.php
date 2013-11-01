<?php
/*
SPOOL UP 3 WORKERS
Coded Expanded on From Ian Barber <ian(dot)barber(at)gmail(dot)com> Example on zeroMQ
*/

include("config.php");
include("class.endpoints.php");

define("NBR_WORKERS", 3);
echo "starting spool/router...".PHP_EOL;

//  Worker using REQ socket to do LRU routing
function worker_thread ()
{
	$identity = sprintf ("%04X", rand(0, 0x10000));

    $context = new ZMQContext();
    $worker = $context->getSocket(ZMQ::SOCKET_REQ);
    $worker->connect("ipc://backend.ipc");
	echo "started a worker...".PHP_EOL;
    //  Tell broker we're ready for work
    $worker->send("READY");
	echo "$identity worker says READY...".PHP_EOL;

    while (true) {
        //  Read and save all frames until we get an empty frame
        //  In this example there is only 1 but it could be more
        $address = $worker->recv();

        // Additional logic to clean up workers.
        if ($address == "END") {
            exit();
        }
        $empty = $worker->recv();
        assert(empty($empty));

        //  Get request, send reply
        $request = $worker->recv();
		
		# decode the request and get our api functions... 
		$arrClientInstructions = json_decode($request, 1);
		
		// what are we trying to do??
		// Here we can add more api end points.
		// just add an end point method to the class "class.endpoint.php"
		// inside each method you can validate what you're expecting. We don't
		// do it here because it may vary depending on endpoint purpose
		//////////////////////////////////////////////////////////////////

		$oEndPoint = new endpoints();
		$arrResponseToClient = $oEndPoint->go($arrClientInstructions[strProvidedURL], $arrClientInstructions[strEndPoint]);

		//////////////////////////////////////////////////////////////////
	

        echo ("$identity Worker Received: $request FROM $address".PHP_EOL);

		if (!$arrResponseToClient)
		{
			$arrResponseToClient[nStatus]=0; # failure
		}

        $worker->send($address, ZMQ::MODE_SNDMORE);
        $worker->send("", ZMQ::MODE_SNDMORE);
        $worker->send(json_encode($arrResponseToClient));
    }
}

function main()
{

    for ($worker_nbr = 0; $worker_nbr < NBR_WORKERS; $worker_nbr++) {
        $pid = pcntl_fork();
        if ($pid == 0) {
            worker_thread();

            return;
        }
    }

    $context = new ZMQContext();
    $frontend = new ZMQSocket($context, ZMQ::SOCKET_ROUTER);
    $backend = new ZMQSocket($context, ZMQ::SOCKET_ROUTER);
    $frontend->bind("tcp://*:15000");
    $backend->bind("ipc://backend.ipc");

    //  Queue of available workers
    $available_workers = 0;
    $worker_queue = array();
    $writeable = $readable = array();

    while (true) {
        $poll = new ZMQPoll();

        //  Poll front-end only if we have available workers
        if ($available_workers > 0) {
            $poll->add($frontend, ZMQ::POLL_IN);
        }

        // Always poll for worker activity on backend
		// This way we can check the ready state from our workers
        $poll->add($backend, ZMQ::POLL_IN);
        $events = $poll->poll($readable, $writeable);

        if ($events > 0) {
            foreach ($readable as $socket) {
                //  Handle worker activity on backend
                if ($socket === $backend) {
                    //  Queue worker address for LRU routing
                    $worker_addr = $socket->recv();
                    assert($available_workers < NBR_WORKERS);
                    $available_workers++;
                    array_push($worker_queue, $worker_addr);

                    //  Second frame is empty
                    $empty = $socket->recv();
                    assert(empty($empty));

                    //  Third frame is READY or else a client reply address
                    $client_addr = $socket->recv(); #3
					
					// so it wasn't ready therefore it must be the client reply address: $client_addr
                    if ($client_addr != "READY") {
                        $empty = $socket->recv(); #4 is blank http://zguide.zeromq.org/php:chapter3 FIG 34
                        assert(empty($empty));
                        $reply = $socket->recv(); #5 is the message

						// sending a 3 frame message (client address|blank|reply)
                        $frontend->send($client_addr, ZMQ::MODE_SNDMORE);
                        $frontend->send("", ZMQ::MODE_SNDMORE);
                        $frontend->send($reply);

                        // exit after all messages relayed
                       // $client_nbr--;
                    }
                } elseif ($socket === $frontend) {
                    //  Now get next client request, route to LRU worker
                    //  Client request is [address][empty][request]
                    $client_addr = $socket->recv();
                    $empty = $socket->recv();
                    assert(empty($empty));
                    $request = $socket->recv();

                    $backend->send(array_shift($worker_queue), ZMQ::MODE_SNDMORE);
                    $backend->send("", ZMQ::MODE_SNDMORE);
                    $backend->send($client_addr, ZMQ::MODE_SNDMORE);
                    $backend->send("", ZMQ::MODE_SNDMORE);
                    $backend->send($request);

                    $available_workers--;
                }
            }
        }
    }

    sleep(1);
}

main();

?>