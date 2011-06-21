<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Code Sample PayPal Brasil: Pagamentos Recorrentes com Express Checkout</title>
		<style type="text/css">
			#ec-button {
				cursor: pointer;
				margin-right: 7px;
			}
		</style>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
	</head>
	<body>
		<form id="checkout" action="checkout.php" method="post">
			<span>Valor</span><span>R$ 9,99</span><br />
                        <span>Quantidade de parcelas</span>
                        <span><select name="periodo" id="periodo">
                                <option value="0">Sem data para término</option>
                            <option value="6">6 Meses</option>
                            <option value="12">12 meses</option>
                            </select></span><br />

			<img id="ec-button" src="https://www.paypal.com/pt_BR/i/btn/btn_subscribe_LG.gif" onclick="checkout.submit();" />
		</form>
	</body>
</html>