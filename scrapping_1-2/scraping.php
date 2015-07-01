<?php

   /**
    *  @author: Nayak Kamal
    *  @Description : This Class is used to fetch details from external Website and Store Data in database
    * 
    **/
    
    include_once('simple_html_dom.php');

	$localhost='localhost';
	$userName='root';
	$password='123456';
	$dbName='product';

	// use mysqli_connect because mysql_connect is Deprecated
	$connectionObj=mysqli_connect($localhost,$userName,$password,$dbName) or die("Error " . mysql_error($link));

	function scraping_test($url,$num,$conn) {
		// create HTML DOM
		$url = ($num==1) ? $url : $url.'?s='.$num;
		
		$html = file_get_html($url);

		// get Whole class of each product details
		$newVar=$html->find('p[class="row"]');
		$productInformation = array();
		
		foreach($newVar as $key=>$products) {
			
		   $price = trim($products->find('span.price', 0)->plaintext);
		   
		   $title= trim($products->find('a.hdrlnk', 0)->plaintext);
		   // remove $ sign from price
		   $price = ($price == "") ? '0' : str_replace("&#x0024;","",$price);
		   // We have to create text file to save Data in insert queries
		   if($title != '' && $price != '' ) {
			 $str = "INSERT INTO product_details(id,title,price) VALUES (NULL,'".addslashes($title)."',$price);\n";
		   }
		   $result = insertQuery($str,$conn);
		   
		}
		$html->clear();
		unset($html);

		return $ret;
	}
	
	// This function used to Insert Query in Database
	function insertQuery($query,$conn){
		return mysqli_query($conn,$query) or die('cant insert in database'.mysql_errno());

	}

    // The Main function executed from Here

	for($i=1;$i<1000;$i=$i+100) {
	  $ret = scraping_test('http://newyork.craigslist.org/search/bka',$i,$connectionObj);
	}

	echo "Products Migration DOne Sucessfully";
?>
