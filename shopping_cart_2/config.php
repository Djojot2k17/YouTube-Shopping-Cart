<?php 
define('BASEURL', $_SERVER['DOCUMENT_ROOT'] . '/youtube/shopping_cart_2/');
define('CART_COOKIE' , 'asdfasd654a6s5465465a4sd');
define('CART_COOKIE_EXPIRE' , time() + (86400 *30));
define('TAXRATE',0.087); //sales tax rate. Set to 0 if no tax is charged
//the stuff under here is for stripe
define('CURRENCY', 'usd');
define('CHECKOUTMODE', 'TEST'); //change test to LIVE when ready to go live

if(CHECKOUTMODE == 'TEST'){
	define('STRIPE_PRIVATE','sk_test_xcRtH1yWOIhUCDARR6SzF7yx');
	define('STRIPE_PUBLIC','pk_test_auPbyzWlNLCObGxC3HH8WymU');
}

if(CHECKOUTMODE == 'LIVE'){
	define('STRIPE_PRIVATE','');// requires account activation on stripe.com
	define('STRIPE_PUBLIC','');
}