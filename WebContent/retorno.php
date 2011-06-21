<?php

if ( isset( $_GET[ 'token' ] ) ) {
	$token = $_GET[ 'token' ];

	$nvp = array(
		'TOKEN'                         => $token,
		'METHOD'			=> 'GetExpressCheckoutDetails',
		'VERSION'			=> '72.0',
		'PWD'				=> 'PWD',
        'USER'				=> 'USR',
        'SIGNATURE'			=> 'SIGNATURE',
	);

	$curl = curl_init();

	curl_setopt( $curl , CURLOPT_URL , 'https://api-3t.sandbox.paypal.com/nvp' );
	curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
	curl_setopt( $curl , CURLOPT_RETURNTRANSFER , 1 );
	curl_setopt( $curl , CURLOPT_POST , 1 );
	curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query( $nvp ) );

	$response = urldecode( curl_exec( $curl ) );
	$responseNvp = array();

	if ( preg_match_all( '/(?<name>[^\=]+)\=(?<value>[^&]+)&?/' , $response , $matches ) ) {
		foreach ( $matches[ 'name' ] as $offset => $name ) {
			$responseNvp[ $name ] = $matches[ 'value' ][ $offset ];
		}
	}

	if ( isset( $responseNvp[ 'TOKEN' ] ) && isset( $responseNvp[ 'ACK' ] ) ) {
		if ( $responseNvp[ 'TOKEN' ] == $token && $responseNvp[ 'ACK' ] == 'Success' ) {
			$nvp[ 'PAYERID' ]				= $responseNvp[ 'PAYERID' ];
			$nvp[ 'PAYMENTREQUEST_0_AMT' ]			= $responseNvp[ 'PAYMENTREQUEST_0_AMT' ];
			$nvp[ 'PAYMENTREQUEST_0_CURRENCYCODE' ]		= $responseNvp[ 'PAYMENTREQUEST_0_CURRENCYCODE' ];
			$nvp[ 'PAYMENTREQUEST_0_PAYMENTACTION' ]	= 'Sale';
                        $nvp[ 'AMT' ]                                   = '9.99'; //Valor que será cobrado
                        $nvp[ 'PROFILESTARTDATE'] = date(DATE_ATOM);
                        $nvp[ 'DESC' ]                                  = 'Acesso a conteúdo exclusivo. R$9,90 por mês.'; //Deve bater com L_BILLINGAGREEMENTDESCRIPTION0 do SetEC
                        $nvp[ 'BILLINGPERIOD' ]                         = 'Month'; //as opções são Day, SemiMonth, Month e Year - aparentemente case sensitive.
                        $nvp[ 'BILLINGFREQUENCY' ]                      = '1'; //Para cobrar todo BillingPeriod, nesse caso, mensalmente
                        $nvp[ 'TOTALBILLINGCYCLES' ]                    = $_GET[ 'PERIODO' ]; //Quanto tempo ficará ativo
                        $nvp[ 'METHOD' ]				= 'CreateRecurringPaymentsProfile';

			curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query( $nvp ) );

			$response = urldecode( curl_exec( $curl ) );
                        
			$responseNvp = array();

			if ( preg_match_all( '/(?<name>[^\=]+)\=(?<value>[^&]+)&?/' , $response , $matches ) ) {
				foreach ( $matches[ 'name' ] as $offset => $name ) {
					$responseNvp[ $name ] = $matches[ 'value' ][ $offset ];
				}
			}

			if ( $responseNvp[ 'PROFILESTATUS' ] == 'ActiveProfile' ) {
				$resultado = 'Parabéns, sua assinatura foi concluída com sucesso';
			} else {
				$resultado = 'Não foi possível concluir a transação';
			}
		} else {
			$resultado = 'Não foi possível concluir a transação';
		}
	} else {
		$resultado = 'Não foi possível concluir a transação';
	}

	curl_close( $curl );
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
  <body>
    <?= $resultado ?>
  </body>
</html>