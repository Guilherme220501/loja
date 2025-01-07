const express = require('express');
const stripe = require('stripe')('sk_live_...UT9D'); // Substitua pela sua chave secreta real
const bodyParser = require('body-parser');
const app = express();

// Middleware para analisar JSON
app.use(bodyParser.json());

// Endpoint para processar pagamento
app.post('/pagamento', async (req, res) => {
    const { token, amount } = req.body;

    try {
        const pagamento = await stripe.paymentIntents.create({
            amount: amount, // valor em centavos (ex: 1000 = R$10,00)
            currency: 'brl', // Moeda
            payment_method: token.id, // O token gerado no front-end
            confirm: true, // Confirma automaticamente a transação
        });

        res.status(200).send({
            success: true,
            paymentIntentId: pagamento.id,
        });
    } catch (error) {
        console.error(error);
        res.status(500).send({
            error: error.message,
        });
    }
});

// Iniciar o servidor
app.listen(3000, () => {
    console.log('Servidor rodando na porta 3000');
});
