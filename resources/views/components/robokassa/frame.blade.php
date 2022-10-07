<script type='text/javascript' src='https://auth.robokassa.ru/Merchant/bundle/robokassa_iframe.js'></script>
<input type='submit' {{ $attributes->merge(['style' => '', 'class' => 'btn mt-2 mb-4']) }} value='{{ $slot }}' id="robokassa">
<script>
	document.getElementById('robokassa').addEventListener('click', () => {
		Robokassa.StartPayment({
			MerchantLogin: '{{ $getMerchant() }}',
			Shp_Mail: '0',
			Shp_Session: '{{ session()->getId() }}',
			OutSum: '{{ $getSum() }}',
			InvoiceID: '{{ $getInvoice() }}',
			Email: '{{ $getEmail() }}',
			Description: '{{ $getDescription() }}',
			Culture: 'ru',
			IsTest: '0',
			Encoding: 'utf-8',
			SignatureValue: '{{ md5(
    			sprintf("%s:%s:%s:%s:Shp_Mail=%d:Shp_Session=%s",
					$getMerchant(), $getSum(), $getInvoice(), $getPassword(), 0, session()->getId()
				)
				) }}'
		})
	}, false);
</script>
