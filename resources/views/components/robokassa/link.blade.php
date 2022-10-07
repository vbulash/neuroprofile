<a href="
https://auth.robokassa.ru/Merchant/Index.aspx?
&MerchantLogin={{ $getMerchant() }}
&Shp_Mail=1
&Shp_Session={{ session()->getId() }}
&OutSum={{ $getSum() }}
&InvoiceID={{ $getInvoice() }}
&Email={{ $getEmail() }}
&Description={{ $getDescription() }}
&Culture=ru
&IsTest=0
&Encoding=utf-8
&SignatureValue={{ md5(sprintf("%s:%s:%s:%s:Shp_Mail=%d:Shp_Session=%s", $getMerchant(), $getSum(), $getInvoice(), $getPassword(), 1, session()->getId())) }}">
	{{ $slot }}
</a>
