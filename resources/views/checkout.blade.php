{{-- <!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="https://assets.edlin.app/favicon/favicon.ico">

    <link rel="stylesheet" href="https://assets.edlin.app/bootstrap/v5.3/bootstrap.css">

    <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.client_id') }}&currency=GBP&intent=capture">
    </script>

    <!-- Title -->
    <title>PayPal Laravel</title>
</head>

<body>
    <div class="container text-center">
        <div class="row mt-5">
            <div class="col-12">

                <div class="row mt-3" id="paypal-success" style="display: none;">
                    <div class="col-12 col-lg-6 offset-lg-3">
                        <div class="alert alert-success" role="alert">
                            You have successfully sent me money! Thank you!
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12 col-lg-6 offset-lg-3" id="payment_options"></div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    paypal.Buttons({
        createOrder: function() {
            return fetch("/paypal/create/10")
                .then((response) => response.text())
                .then((id) => {
                    return id;
                });
        },

        onApprove: function() {
            return fetch("/paypal/complete", {
                    method: "post",
                    headers: {
                        "X-CSRF-Token": '{{ csrf_token() }}'
                    }
                })
                .then((response) => response.json())
                .then((order_details) => {
                    console.log(order_details);
                    document.getElement
                    ById("paypal-success").style.display = 'block';
                    //paypal_buttons.close();
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        onCancel: function(data) {
            //todo
        },

        onError: function(err) {
            //todo
            console.log(err);
        }
    }).render('#payment_options');
</script>

</html> --}}

<div id="paypal-button-container-P-4YA42591RL127190NMYDT5MA"></div>
<script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.client_id') }}&vault=true&intent=subscription"
    data-sdk-integration-source="button-factory"></script>
<script>
    paypal.Buttons({
        style: {
            shape: 'rect',
            color: 'gold',
            layout: 'vertical',
            label: 'subscribe'
        },
        createSubscription: function(data, actions) {
            return actions.subscription.create({
                /* Creates the subscription */
                plan_id: 'P-4YA42591RL127190NMYDT5MA'
            });
        },
        onApprove: function(data, actions) {
            alert(data.subscriptionID); // You can add optional success message for the subscriber here
        }
    }).render('#paypal-button-container-P-4YA42591RL127190NMYDT5MA'); // Renders the PayPal button
</script>
<form action="/paypal/create?paypal_plan_id=P-4YA42591RL127190NMYDT5MA" method="POST">
    <button>Pay</button>
</form>
