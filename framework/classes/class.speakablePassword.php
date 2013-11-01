<?
// In order for the driver classes to work you need to have a
// strDriverPath column in the modules database table //

class speakablePassword {

	function speakablePassword($Len = "4"){ 
	$Vocali = array(a,e,i,o,u); 
	$Dittonghi = array(ae,ai,ao,au,ea,ei,eo,eu,ia,ie,io,iu,ua,ue,ui,uo); 
	$Cons = array(b,c,d,f,g,h,k,l,n,m,p,r,s,t,v,z); // Consonanti senza q 
	$ConsDoppie = array(bb,cc,dd,ff,gg,ll,nn,mm,pp,rr,ss,tt,vv,zz); 
	$ConsAmiche = array(bl,br,ch,cl,cr,dl,dm,dr,fl,fr,gh,gl,gn,gr,lb,lp,ld,lf,lg,lm,lt,lv,lz,mb,mp,nd,nf,ng, 
						 nt,nv,nz,pl,pr,ps,qu,rb,rc,rd,rf,rg,rl,rm,rn,rp,rs,rt,rv,rz,sb,sc,sd,sf,sg,sl,sm,sn,sp, 
						 sr,st,sv,tl,tr,vl,vr); 
	$listaVocali = array_merge($Vocali,$Dittonghi); 
	$listaCons = array_merge($Cons,$ConsDoppie,$ConsAmiche); 
	$nrVocali = sizeof($listaVocali); 
	$nrConsonanti = sizeof($listaCons); 

	$Loop = $Len; 

		if(rand(1,10) > 5){ 
			// La prima lettera deve essere una Consonante singola     
			$Password = $Cons[rand(1,sizeof($Cons))]; 
			$Password .= $listaVocali[rand(1,$nrVocali)]; 
			$inizioC = true; 
			$Loop--; 
		} 
		// Inizia la generazione della Password 
		for($i=0; $i<$Loop; $i++){ 
			$qualeV = $listaVocali[rand(1,$nrVocali)]; 
			$qualeC = $listaCons[rand(1,$nrConsonanti)]; 
			if($inizioC){ 
				$Password .= $qualeC.$qualeV; 
				}else{ 
					$Password .= $qualeV.$qualeC; 
			} 
		} 
		// Taglio la password al numero massimo di caratteri richiesto 
		$Password = substr($Password,0,$Len); 
		// Se le ultime due lettere della stringa sono doppie l'ultima viene sostituita con una vocale 
		if ( in_array(substr($Password,($Len-2),$Len),$ConsDoppie)){ 
			$Password = substr($Password,0,($Len-1)).$listaVocali[rand(1,$nrVocali)]; 
			} 
		return $Password; 
	}
}
