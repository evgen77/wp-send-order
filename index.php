
<?php
/*
Plugin Name:Отправка данных о заказе 
Version: 1.1
Plugin URI: https://rezina.run/
Description:Отправка данных о заказе на телеграм илил еще куда то по курл
Author: @digital_king

*/


function sent_telegram( $order_info ) {
	if($order_info){


	foreach($order_info as $order){

		$msg .= '#'.$order['order_id'];
		$msg .= "\n";

		if($order['items']){
			$i =0;
			foreach($order['items'] as $item){  $i++;
				
				$msg .=  "<b>".$i .'. '.$item['name']."</b>";
				$msg .= "\n";
				if($item['variations']){
					// $msg .= "<b>Додатки: </b>"."\n";
					
					foreach($item['variations'] as $var){					
						$msg .= $var['label'].' '.$var['val']."\n";
					
					}
				}
				if($item['atrb']){
					$msg .= "<b>Додатки: </b>"."\n";
					
					foreach($item['atrb'] as $var){					
						$msg .= $var['label'].' '.$var['val']."\n";
					
					}
				}
			}
			
		}
		$msg .= "\n\n";
		$msg .= "<b>Спосіб оплати: </b>" .$order['payment'] . '';
		$msg .= "\n";
		$msg .= "<b>Доставка: </b>" . $order['shipping_total'] .' грн';
		$msg .= "\n";
		$msg .= "<b>Зона доставки: </b>" .$order['shipping'];
		if($order['comment_note']){
			$msg .= "\n";
			$msg .= "<b>Нотатка: </b>" .$order['comment_note'];
		}
		if($order['additional_cash']){
			$msg .= "\n";
			$msg .= "<b>Підготувати решту з: </b>" .$order['additional_cash'];
		}
		$msg .= "\n";
		$msg .= "<b>Загальна вартість: </b>" .$order['total'] .'грн';
		$msg .= "\n";
	
		$msg 	.="<b>Ім‘я: </b>" .$order['name'];
		$msg .= "\n";
		$msg 	.="<b>Телефон: </b>" .$order['phone'];
		$msg .= "\n";
		$msg 	.="<b>Адреса: </b>" .$order['address']; 

	}


    $userId = '-1001679895586'; // Ваш id в телеграм
    $token = '5702131517:AAE2lNejlfuBx7V4W1Lr27IHczyR4JoECWI'; // Token бота

    // $userId = '332646331'; // Ваш id в телеграм
    // $token = '1122657577:AAFrbpVC00jxC5HeIzjQTv-YZ_YO_gC2GbQ'; // Token бота

	file_get_contents('https://api.telegram.org/bot'. $token .'/sendMessage?chat_id='. $userId .'&text=' . urlencode($msg) . '&parse_mode=html'); // Отправляем сообщение
}
}

add_action( 'woocommerce_new_order', 'send_order_details', 10, 2 );
function send_order_details( $order_id, $order){
	// $order = wc_get_order(  $order_id );
	// get data order
	
	
	$data = $order->get_data();


	// get products
	$order_items = $order->get_items();

	
	$items = array();
// comment
	if($order->get_customer_note() ){
		$comment	= $order->get_customer_note();
	} else{
		$comment = '';
	}
	


	// exit;
// shipping
	if($order->get_shipping_method()){
		$shipping = $order->get_shipping_method();
		$shipping_total =  $order->get_shipping_total();
	} else{
		$shipping = '';
		$shipping_total = '';
	}
	
	if($order_items){
foreach( $order_items as $item_id => $item ){

	// методы класса WC_Order_Item

	// ID элемента можно получить из ключа массива или так:
	$item_id = $item->get_id();

	// методы класса WC_Order_Item_Product

	$item_name = $item->get_name(); // Name of the product
	$item_type = $item->get_type(); // Type of the order item ("line_item")

	$product_id = $item->get_product_id(); // the Product id
	$wc_product = $item->get_product(); // the WC_Product object

	// данные элемента заказа в виде массива
	$item_data = $item->get_data();
	// var_dump($item_data);
	// var_dump($item_data);




	$attributes = $item->get_meta_data();

	// var_dump($item['variation_id']);

	// $vars    = $item['variation_id'] ? $item->get_variation( $item['variation_id'] ) : false;
	

	// var_dump($vars);

	// var_dump($item_data);
	// echo '111';
	// var_dump($attributes);

	$meta_data_as_array = array();
	$atrb = array();
	$variations = array();
	
    if($attributes){
	foreach( $attributes as $attr ) {
		// gett data
        $meta_data_as_array = $attr->get_data();

	
		// get terms
		$meta = get_post_meta($item['variation_id'], 'attribute_'.$meta_data_as_array['key'], true);

		
		$term = get_term_by('slug', $meta, $meta_data_as_array['key']);
		// var_dump($meta_data_as_array);
		

		// sett attr array
		if($term->name){
			$variations[] = array(
				'label'	=> wc_attribute_label($meta_data_as_array['key']),
				'val'	=> $term->name,
		);
	
		// set addons(options)
		}  elseif( $meta_data_as_array['key'] !== 'pa_myaso-na-vybir' || $meta_data_as_array['key'] !== 'napij-do-menyu') {
			$atrb[] = array(
				'label'	=>	$meta_data_as_array['key'],
				'val'	=> $meta_data_as_array['value'],
		);
		}
	

		// var_dump($attr->get_data());
		// echo $meta_data_as_array[0]['key'];

		// if( $meta_data_as_array[0]['key'] == 'pa_myaso-na-vybir' || $meta_data_as_array[0]['key'] == 'pa_myaso-na-vybir') {
		// 	// $addons[] =   $metas;
		// 	echo '<br>';
		// 	echo $meta_data_as_array[0]['key'];
		// }
			
		
			// foreach($attr->get_data() as $metas){

				
			// 	if($metas !== 'pa_myaso-na-vybir' || $metas !== 'napij-do-menyu') {
			// 		$addons[] =   $metas;
		
		
			// 	}
			// }
		
	}

	
	}
	
// change total 
	if($item->get_quantity() > 1){
		$tot = $item->get_total() /$item->get_quantity();
	} else{
		$tot =  $item->get_total();

	}
		// file_put_contents($file,'logged value:'.var_dump($tot ));
		// exit;
	// var_dump($addons);
	// var_dump($atrb);
	$items[] =array(
		'name'	=> $item_name . '( '.$item->get_quantity().':'.$tot .' )',
		'atrb'	=>$atrb,
		'variations'	=>$variations
		// 'qnt'	=> $item_data['quantity'],
		// 'addons'	=>$meta_data_as_array,
				
	);


	// var_dump($item_data);
	$total = $item_data['total'];
	// echo $item_data['name'];
	// echo $item_data['product_id'];
	// echo $item_data['variation_id'];
	// echo $item_data['quantity'];
	// echo $item_data['tax_class'];
	// echo $item_data['subtotal'];
	// echo $item_data['subtotal_tax'];
	// echo $item_data['total'];
	// echo $item_data['total_tax'];

}
	// var_dump($items);
}
if($order->get_billing_company()){
	$additional_cash = $order->get_billing_company();

} else{
	$additional_cash = $order->get_billing_company();

}


// var_dump($additional_cash);
// exit;
$order_info[] = array(
	'order_id' =>$data['id'],

	// 'order_id' =>1,
	'date'		=> $data['date_created']->date('Y-m-d H:i:s'),
	'payment'	=> $data['payment_method_title'],
	'shipping'	=> $shipping ,
	'shipping_total'	=> $shipping_total,
	'name'	=> $data['shipping']['first_name'],	
	'phone'			=> $data['billing']['phone'],
	'address'		=> $data['shipping']['address_1'] .' '.$data['shipping']['address_2'],
	'comment_note'	=> $comment,
	'items'			=> $items,
	'total'    => $order->total,
	'additional_cash'	=> $additional_cash
);

// $file = 'log.txt';
// file_put_contents($file,'logged value:'.var_dump($order_info));
// exit;


// var_dump($order_info);
if($order_info){
	sent_telegram($order_info);

	$send_order = json_encode($order_info);

	$url = "http://cloud-server.work:680/Tamash_Kebab_API/hs/API/Tilda";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$send_order);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch);
	curl_close($ch);




}
// var_dump($order_info);

}


