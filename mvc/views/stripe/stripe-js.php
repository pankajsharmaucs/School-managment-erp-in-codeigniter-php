
<script src="https://js.stripe.com/v3/"></script>
<script>

    let stripeKey = '<?=$payment_options['stripe_key']?>';
    let currencyCode = '<?=$siteinfos->currency_code?>';
    let stripe = Stripe(stripeKey);
    let elements = stripe.elements();
    let style = {
        base: {
            fontSize: '16px',
            color: '#32325d',
            border:'1px solid red',
        },
    };

    let card = elements.create('card', {style: style});
    card.mount('#card-element');

    function stripe_payment()
    {
        $('#payment_method').parent().removeClass('has-error');
        stripe.createToken(card).then(function (result) {
            if (result.error) {
                let errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                stripeTokenHandler(result.token);
            }
        });

    }

    function stripeTokenHandler(token)
    {
        var form = document.getElementById('paymentAddDataForm');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        form.appendChild(hiddenInput);
        form.submit();
    }
</script>