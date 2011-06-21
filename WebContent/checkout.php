<?php
$total = 0.00; //Nao eh uma compra e por este motivo o valor deve ser ZERO

$nvp = array(
	'PAYMENTREQUEST_0_AMT'			=> $total,
	'PAYMENTREQUEST_0_CURRENCYCODE'		=> 'BRL',
	'PAYMENTREQUEST_0_PAYMENTACTION'	=> 'Sale',
    'LOCALECODE'                            => 'pt_BR', //pt_BR forca experiencia em portugues
    'L_BILLINGTYPE0'                        => 'RecurringPayments', //Necessario informar o tipo de pagamento
    'L_BILLINGAGREEMENTDESCRIPTION0'        => 'Acesso a conteudo exclusivo. R$9,90 por mes.', //adicione uma descricao da assinatura
	'RETURNURL'				=> 'http://127.0.0.1/RecurringPayments/retorno.php?PERIODO='.$_GET['periodo'],
	'CANCELURL'				=> 'http://127.0.0.1/RecurringPayments/cancelamento.php',
	'METHOD'				=> 'SetExpressCheckout',
	'VERSION'				=> '72.0',
	'PWD'					=> 'PWD',
	'USER'					=> 'USR',
	'SIGNATURE'				=> 'SIGNATURE',


);

$curl = curl_init();

curl_setopt( $curl , CURLOPT_URL , 'https://api-3t.sandbox.paypal.com/nvp' );
curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
curl_setopt( $curl , CURLOPT_RETURNTRANSFER , 1 );
curl_setopt( $curl , CURLOPT_POST , 1 );
curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query( $nvp ) );

$response = urldecode( curl_exec( $curl ) );

curl_close( $curl );

$responseNvp = array();

if ( preg_match_all( '/(?<name>[^\=]+)\=(?<value>[^&]+)&?/' , $response , $matches ) ) {
	foreach ( $matches[ 'name' ] as $offset => $name ) {
		$responseNvp[ $name ] = $matches[ 'value' ][ $offset ];
	}
}

if ( isset( $responseNvp[ 'ACK' ] ) && $responseNvp[ 'ACK' ] == 'Success' ) {
	$paypalURL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	$query = array(
		'cmd'	=> '_express-checkout',
		'token'	=> $responseNvp[ 'TOKEN' ]
	);

	header( 'Location: ' . $paypalURL . '?' . http_build_query( $query ) );
} else {
	echo 'Falha na transação  ' . $response;
}