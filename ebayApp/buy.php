<!--
Author Name: Ahmed Ulde
URL Address: http://omega.uta.edu/~aau0889/project3/buy.php
-->
<!DOCTYPE HTML>
<html>
<head><title>Buy Products</title></head>
<body>
<fieldset>
Shopping Basket:
<?php 
	//$s_array=array();//array for storing sessions data
	session_start();//start session
	
	//if url has get[clear] super global variable set then clear all content of session
	if(isset($_GET['clear'])){
		if($_GET['clear']==1)
		session_unset();
	}
	
	//if url has deleted a product id take that id and delete that product from basket
	if(isset($_GET['delid'])){
		$temp="'".$_GET['delid']."'";
		unset($_SESSION[$temp]);
	}
	
	//if url has id of product to buy add it to session and display in basket
	if(isset($_GET['buy'])){
		$temp="'".(String)$_GET['buy']."'";
		$_SESSION[$temp]=$_GET['buy'];
	}
	
	//start of table in the basket
	echo('<table border="1px">');
	$totalcost=0;$boolean=1;

	foreach($_SESSION as $index=>$id){
	//send request to search by product id
	//this loop traverses the session array and gets the product id then send to ebay and display the response.
		$url='http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&productId='.$id;
		$resp=new SimpleXMLElement(file_get_contents($url));
		echo $resp;
		
	//extracting values from XML response
		$prod_name_b=$resp->categories->category->items->product->name;
		$prod_img=$resp->categories->category->items->product->images->image[0]->sourceURL;
		$cost=$resp->categories->category->items->product->minPrice;
		$desc=$resp->categories->category->items->product->fullDescription;
		$totalcost=$totalcost+$cost;
		$produrl=$resp->categories->category->items->product->productOffersURL;
		
	//display elements in basket
		if($boolean==1){//so that heading row is displayed once while creating table only
			echo('<tr><td>Click On Image for Exciting offers</td><td>NAME</td><td>DESCRIPTION</td><td>PRICE</td></tr>');
			$boolean=0;
		}
		echo('<tr>');
		echo('<td><a href="'.$produrl.'"><img src="'.$prod_img.'"/></a></td>');//linking image in basket to shopping.com
		echo('<td>'.$prod_name_b.'</td>');
		echo('<td>'.$desc.'</td>');
		echo('<td>'.$cost.'</td>');
		//echo('<td>'.$produrl.'</td>');
		echo('<td><a href="buy.php?delid='.$id.'">DELETE</a></td>');
		echo('</tr>');
	}
	
	echo('</table>');
	echo("<p>Total Amount=>".$totalcost."USD</p>");//refresh
	//print_r($_SESSION);
	//echo ("<p>".session_id()."</p>");

?>

<a href="buy.php?clear=1">EMPTY BASKET</a>
</fieldset>

<form action="buy.php" method="GET">
<label>Category:</label>

<?php
	error_reporting(E_ALL);//turn on all error reports
	ini_set('display_errors','On');
	//$xmlstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&trackingId=7000610&keyword=sony+vaio');
	$xmlstr=file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/CategoryTree?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId=72&showAllDescendants=true');
	$xml = new SimpleXMLElement($xmlstr);
	
	//content type text/html displays html but text/xml displays the DOM Tree
	header('Content-Type: text/html');
	$x=$xml->category[0];
	echo('<select name="category">');
	$count1=count($x->categories->category);
	for($i=0;$i<$count1;$i++){
	$name=$x->categories->category[$i];
	$name2=$name->name;
	$val=$name['id'];
	echo('<optgroup label="'.$name2.'">');//grouping the elements using labels
	echo('<option value="'.$val.'">'.$name2.'</option>');
	$count2=count($x->categories->category[$i]->categories->category);
	//echo("<p>".$count2."</p>");
	for($j=0;$j<=$count2;$j++){
			$name3=$x->categories->category[$i]->categories->category[$j];
			$name4=$name3->name;
			$val2=$name3['id'];
			echo('<option value="'.$val2.'">'.$name4.'</option>');
		}
	}
	echo('</select>');
?>


<br/><label>Keywords:</label>
<input type="text" name="keyw"></input><br/>
<input type="submit"></input>
</form>

<?php
if($_GET){
    if(isset($_GET['keyw'])||isset($_GET['category'])){
        send();
		//clear GET 
    }
}

function send(){
	$keywords=urlencode($_GET['keyw']);
	$cid=urlencode($_GET['category']);
	$url='http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&trackingId=7000610&categoryId=72&category='.$cid.'&keyword='.$keywords.'&numItems=20';
	$xmlstring=file_get_contents($url);
	$xmls=new simpleXMLElement($xmlstring);
	echo('<table border="1px">');
	echo('<tr><td>Click on Image to add to basket</td><td>NAME</td><td>DESCRIPTION</td><td>PRICE</td></tr>');
	$count_p=count($xmls->categories->category->items->product);//counting number of responses.
	for($i=0;$i<$count_p;$i++){
		$name_P=$xmls->categories->category->items->product[$i]->name;
		echo('<tr>');
		$Prod=$xmls->categories->category->items->product[$i];
		$prod_id=$Prod['id'];
		//echo('<td>'.$prod_id.'</td>');
		$src=$xmls->categories->category->items->product[$i]->images->image[0]->sourceURL;
		echo('<td><a href="buy.php?buy='.$prod_id.'"><img src="'.$src.'"/></a></td>');
		echo('<td>'.$name_P.'</td>');
		$description=$Prod->fullDescription;
		echo('<td>'.$description.'$</td>');
		$minprice=$xmls->categories->category->items->product[$i]->minPrice;
		echo('<td>'.$minprice.'$</td>');
		echo('</tr>');
		}
	echo('</table>');
	}
?>
</body>
</html>
