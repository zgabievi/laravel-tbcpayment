<!doctype html>
<html lang="en">
<head>
    <title>Redirecting...</title>
    <script>
        function redirect() {
            document.returnform.submit();
        }
    </script>
</head>
<body onLoad="redirect()">
<form name="returnform" action="https://ecommerce.ufc.ge/ecomm2/ClientHandler" method="POST">
    <input type="hidden" name="trans_id" value="{{ $txn_id }}">

    <noscript>
        Please click the submit button below.<br>
        <input type="submit" name="submit" value="Submit">
    </noscript>
</form>
</body>
</html>
