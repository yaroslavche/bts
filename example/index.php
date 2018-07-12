<?php

Error_Reporting(-1);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use Bitshares\Bitshares;

$service = new Bitshares();
// $d = $service->getObjects(['1.2.100', '1.2.1000', '1.2.10000', '1.3.100', '1.3.101', '1.6.17', '1.6.18', '1.6.19']);
// dump($d, $service);
?>

<html>
<head>
</head>
<body>
    <div id="transactions_container">

    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script>
        var socket = new WebSocket('<?php echo $service->getConnectedServer(); ?>');

        socket.onopen = function(event)
        {
            socket.send('{"jsonrpc":"2.0","method":"call","params":[1,"login",["",""]],"id":1}');
            socket.send('{"jsonrpc":"2.0","method":"call","params":[1,"database",[]],"id":2}');
            socket.send('{"jsonrpc":"2.0","method":"call","params":[2,"set_subscribe_callback",[1,true]],"id":3}');
            // socket.send('{"jsonrpc":"2.0","method":"call","params":[2,"cancel_all_subscriptions",[]],"id":4}');
        }

        let transactionHistoryCollection = [];

        socket.onmessage = function(message)
        {
            const data = JSON.parse(message.data);
            if(typeof data.method == 'string' && data.method == 'notice')
            {
                const
                    subscriptionId = data.params[0],
                    subscriptionData = data.params[1][0];
                if(typeof subscriptionData[0] == 'object')
                {
                    jQuery.post('subscribe.php', {subscriptionData}, function(response) {
                        transactionHistoryCollection.unshift(response);
                    });
                    if(transactionHistoryCollection.length > 10) {
                        transactionHistoryCollection.splice(10, 1);
                    }
                }
                jQuery('#transactions_container').html('<div>' + transactionHistoryCollection.join('</div><div>') + '</div>');
            }
        }
    </script>
</body>
</html>
