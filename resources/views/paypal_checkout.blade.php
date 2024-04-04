<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SAAS-Boilerpolate - Paypal</title>
    <style>
        .container {
            display: flex;
            justify-content: center
        }

        #paypal-button-container-P-5S108013FF819440JMYGX7TY {
            margin-top: 100px;
            width: 700px
        }
    </style>
</head>

<body>
    <div class="container">
        <div id="paypal-button-container-P-5S108013FF819440JMYGX7TY"></div>
    </div>
    <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.client_id') }}&vault=true&intent=subscription"
        data-sdk-integration-source="button-factory"></script>
    <script>
        paypal.Buttons({
            style: {
                shape: 'rect',
                color: 'gold',
                layout: 'vertical',
                label: 'paypal'
            },
            createSubscription: function(data, actions) {
                return actions.subscription.create({
                    /* Creates the subscription */
                    plan_id: "{{ request('paypal_plan_id') }}"
                });
            },

            onApprove: function(data, actions) {
                fetch("{{ route('paypal.subscription.continue') }}", {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        method: "PUT",
                        body: JSON.stringify(data)
                    })
                    .then(function(res) {
                        window.location.href = "{{ route('dashboard') }}"
                    })
                    .catch(function(res) {
                        console.log(res)
                    })
            }
        }).render('#paypal-button-container-P-5S108013FF819440JMYGX7TY'); // Renders the PayPal button
    </script>

</body>

</html>
