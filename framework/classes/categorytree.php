<?

# get categories structure
# create the wonderful navigation tree inside this class... 
# its a pain to do it otherways... 

class categorytree
{

	var $HTML;
	var $nPerCol;
	var $colnum;
	var $showopenul;
	var $throughCount;
	var $postToID;
	var $showAll;	

	function categorytree($postToID="3",$showAll="")
	{
		
		$this->postToID=$postToID;
		$this->showAll = $showAll;

		#ok, we know the category count
		$nHowmany = mirage_dbdo("SELECT count(*) as howmany FROM category", "SELECT");		
				
		#how many cols should we be making
		$numCol = 2;

		#how many per col?
		$nPerCol = ceil($nHowmany[howmany]/$numCol);

		#so perhaps we have 10 per column
		$this->nPerCol = $nPerCol;
		$this->colnum=1;
		$this->showopenul = "yes";
		$this->throughCount = 1;
	}

	function makecategorytree($nCategoryID="0",$showOptions="")
	{
		if ($this->showopenul=="yes")
		{
			$this->HTML .="<ul class=\"tree column_".$this->colnum."\">\n";
			$this->showopenul = "no";
			$class = "class=\"root\"";
		} else {
			$this->HTML .="<ul>\n";
		}
		
		// could we do this with an opening category select? see line 25 ish
		$arrCats = mirage_multi("SELECT * FROM category WHERE nCategoryID='$nCategoryID'", 1);		
		
		
		if ($arrCats)
		{
			foreach($arrCats as $foo=>$bar)
			{
				if ($this->showopenul=="yes")
				{
					$this->HTML .="<ul class=\"tree column_".$this->colnum."\">\n";
					$this->showopenul = "no";
					$class = "class=\"root\"";

					if ($lastbar)
					{
					$this->throughCount++;

					if($showOptions!="")
					{
							$this->HTML .= "<li><input type=\"checkbox\" name=\"selected[]\" value=\"$lastbar[id]\" class=\"checkbox\" /><a $class href=\"index.php?id=17&nCategoryID=$lastbar[id]&do=edit\" title=\"{-- lang_dashboard_categories_edit_subcategory_tooltip --}\">$lastbar[strName]</a> 
							<a href=\"index.php?id=17&nCategoryID=$lastbar[id]&do=new\"><img src=\"../themes/default/images/Add_small.gif\" alt=\"{-- lang_dashboard_categories_add_subcategory_tooltip --}\" ></a><a href=\"category_delete.php?nCategoryID=$lastbar[id]\" onClick=\"return confirm('{-- lang_dashboard_categories_dd_delete_category_confirm --}');\"><img src=\"../themes/default/images/Delete_small.gif\" alt=\"{-- lang_dashboard_categories_remove_subcategory_tooltip --}\"></a>\n";
					}
					else
						$this->HTML .= "<li><a $class href=\"index.php?id=$this->postToID&action=search&filter_by_category=$lastbar[id]\">$lastbar[strName]</a>\n";

					# if there are children
					if (mirage_multi("SELECT * FROM category WHERE nCategoryID='$lastbar[id]'", 1))
					{
						$this->makecategorytree($lastbar[id],$showOptions);
					} 
					else 
					{
						$this->HTML .="</li>\n";
					}
					$lastbar="";
					}
				}

				#create the html
				#print "$bar[strName]<br>";
				#print "(($this->throughCount<=$this->nPerCol) || (($bar[nCategoryID]!=0) && ($this->throughCount<=$this->nPerCol)))<br>";
				if (($this->throughCount<=$this->nPerCol) || (($bar[nCategoryID]!=0) && ($this->throughCount<=$this->nPerCol)))
				{
					$this->throughCount++;

					if($showOptions!="")
					{
							$this->HTML .= "<li><input type=\"checkbox\" name=\"selected[]\" value=\"$bar[id]\" class=\"checkbox\" /><a $class href=\"index.php?id=17&nCategoryID=$bar[id]&do=edit\" title=\"{-- lang_dashboard_categories_edit_subcategory_tooltip --}\">$bar[strName]</a> 
							<a href=\"index.php?id=17&nCategoryID=$bar[id]&do=new\"><img src=\"../themes/default/images/Add_small.gif\" alt=\"{-- lang_dashboard_categories_add_subcategory_tooltip --}\" ></a><a href=\"category_delete.php?nCategoryID=$bar[id]\" onClick=\"return confirm('{-- lang_dashboard_categories_dd_delete_category_confirm --}');\"><img src=\"../themes/default/images/Delete_small.gif\" alt=\"{-- lang_dashboard_categories_remove_subcategory_tooltip --}\"></a>\n";
					}
					else
						$this->HTML .= "<li><a $class href=\"index.php?id=$this->postToID&action=search&filter_by_category=$bar[id]\">$bar[strName]</a>\n";

					# if there are children
					# probably a better way than continuing to select the db recs
					if (mirage_multi("SELECT * FROM category WHERE nCategoryID='$bar[id]'", 1))
					{
						$this->makecategorytree($bar[id],$showOptions);
					} 
					else 
					{
						$this->HTML .="</li>\n";
					}
					$lastbar="";
				} else {
					#the col count is done, or the category id is == 0 after we went through the done...
					$this->colnum++;
					$this->showopenul = "yes";
					$this->HTML .="</ul>\n";
					$this->throughCount = 1;

					# past the last one back up... 
					$lastbar = $bar;
				}

			}
		}

			$this->HTML .="</ul>\n";

	}


	
}

?>