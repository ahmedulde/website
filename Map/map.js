// Put your zillow.com API key here
var zwsid = "X1-ZWz1b3odsxak23_8s1xw";
//var googleapi = "AIzaSyDujNGhrLVWOxmPZLdYmZG9np0Gl6e3sA8";

//<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>

var request = new XMLHttpRequest();
var request2=new XMLHttpRequest();
//cited from developer.google.com documentation
  var full_address;
  var geocoder;
  var map;
  var infowindow = new google.maps.InfoWindow();
  var marker;
  var input;

  function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(32.75,-97.13);
    var mapOptions = {
      zoom: 16,
      center: latlng
    }
    map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
    google.maps.event.addListener(map,'click',function(event){input=event.latLng;codeLatLng();});
}

 // document.getElementById("map-canvas").onmousedown()=function{code LatLng();};
var pos;
  function codeLatLng() {
    //google.maps.event.addListener(map,'click',function(event){input=event.latLng;codeLatLng();});
    //var input = document.getElementById("latlng").value;
    var lat = parseFloat(input.k);
    var lng = parseFloat(input.B);
    pos = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'location':pos}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[1]) {
          map.setZoom(11);
          marker = new google.maps.Marker({
              position: pos,
              map: map,
			  title:"THIS IS GEOCODING"
          });
		  console.log(results);
		//display address on marker
		  var disp=results[0].formatted_address;
		  infowindow.setContent(disp);
          infowindow.open(map, marker);
		 //display output
           var ads=document.createTextNode(disp);
	      //out=document.getElementById("output");out.appendChild(ads);	
		var trObj=document.createElement("TR");
		document.getElementById("op").appendChild(trObj);
		var tdObj=document.createElement("TD");
		//var data=document.createTextNode(ads);
		tdObj.appendChild(ads);
		trObj.appendChild(tdObj);
		 //send request to zillow
		 var streetadd=results[0].address_components[0].short_name+results[0].address_components[1].short_name;
		 var city=results[0].address_components[3].short_name;
		 var state=results[0].address_components[5].short_name;
		 var zipcode=results[0].address_components[6].short_name;
		  request2.onreadystatechange=displayResult2;
		  var uri="proxy.php?zws-id="+zwsid+"&address="+streetadd+"&citystatezip="+city+"+"+state+"+"+zipcode;
		  var res=encodeURI(uri);
		  request2.open("GET",res,true);
          request2.withCredentials = "true";
          request2.send(null);
        }
      } else {
        alert("Geocoder failed due to: " + status);
      }
    });
  }

function codeAddress() {
    var address = document.getElementById("address").value;
    geocoder.geocode( { 'address': full_address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location,
			title:full_address
        });
		infowindow.setContent(full_address);
		infowindow.open(map,marker);
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }


function xml_to_string ( xml_node ) {
   if (xml_node.xml)
      return xml_node.xml;
   var xml_serializer = new XMLSerializer();
   return xml_serializer.serializeToString(xml_node);
}

function displayResult () {
    if (request.readyState == 4) {
        var xml = request.responseXML.documentElement;
        var value = xml.getElementsByTagName("zestimate")[0].getElementsByTagName("amount")[0];
		var val=xml_to_string(value);
		var zaddr=xml.getElementsByTagName("address")[1].getElementsByTagName("street")[0].innerHTML+", "+xml.getElementsByTagName("address")[1].getElementsByTagName("city")[0].innerHTML+", "+xml.getElementsByTagName("address")[1].getElementsByTagName("state")[0].innerHTML+", "+xml.getElementsByTagName("address")[1].getElementsByTagName("zipcode")[0].innerHTML;
		//var zad=xml_to_string(zaddr);
		//var tableObj=document.getElementById("op");
	    var trObj=document.createElement("TR");
		document.getElementById("op").appendChild(trObj);
		var tdObj=document.createElement("TD");
		var data=document.createTextNode("Value of "+zaddr+" => "+value.innerHTML);
		tdObj.appendChild(data);
		trObj.appendChild(tdObj);
    }
	}
function displayResult2(){
	if (request2.readyState == 4) {
        var xml2 = request2.responseXML.documentElement;
        var value2 = xml2.getElementsByTagName("zestimate")[0].getElementsByTagName("amount")[0];
	    var val2=xml_to_string(value2);
		var zaddr2=xml2.getElementsByTagName("address")[1].getElementsByTagName("street")[0].innerHTML+", "+xml2.getElementsByTagName("address")[1].getElementsByTagName("city")[0].innerHTML+", "+xml2.getElementsByTagName("address")[1].getElementsByTagName("state")[0].innerHTML+", "+xml2.getElementsByTagName("address")[1].getElementsByTagName("zipcode")[0].innerHTML;
		//document.creatElement("P");
	    //document.getElementById("output").innerHTML=val2;
		var trObj=document.createElement("TR");
		document.getElementById("op").appendChild(trObj);
		var tdObj=document.createElement("TD");
		var data=document.createTextNode("Value of "+zaddr2+" clicked on map=>"+value2.innerHTML);
		tdObj.appendChild(data);
		trObj.appendChild(tdObj);
    }
}

function sendRequest () {
    request.onreadystatechange = displayResult;
    var address = document.getElementById("address").value;
    var city = document.getElementById("city").value;
    var state = document.getElementById("state").value;
    var zipcode = document.getElementById("zipcode").value;
full_address="\""+address+", "+city+", "+state+"\"";
   var uri="proxy.php?zws-id="+zwsid+"&address="+address+"&citystatezip="+city+"+"+state+"+"+zipcode;
		  var res=encodeURI(uri);
		  request.open("GET",res,true);
    request.withCredentials = "true";
    request.send(null);
	codeAddress();
}

function clearbox(){
	document.getElementById("address").value="";
	document.getElementById("city").value="";
	document.getElementById("state").value="";
	document.getElementById("zipcode").value="";
	document.getElementById("output").innerHTML="";
	document.getElementById("op").innerHTML="";
	initialize();
}
