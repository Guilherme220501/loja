const express = require('express');
const stripe = require('stripe')('sk_live_...UT9D'); // Chave secreta completa
const bodyParser = require('body-parser');
const cors = require('cors'); // Para lidar com requisições do front-end
const app = express();

// Middleware para análise de JSON e CORS
app.use(cors());
app.use(bodyParser.json());

// Endpoint para processar pagamento
app.post('/pagamento', async (req, res) => {
    const { paymentMethodId, amount } = req.body; // Recebe o paymentMethodId e o valor total (em centavos)

    try {
        const paymentIntent = await stripe.paymentIntents.create({
            amount: amount, // Valor em centavos
            currency: 'brl', // Moeda em Reais
            payment_method: paymentMethodId, // paymentMethodId gerado no front-end
            confirmation_method: 'manual',
            confirm: true, // Confirma automaticamente a transação
        });

        res.status(200).send({
            success: true,
            paymentIntentId: paymentIntent.id,
        });
    } catch (error) {
        console.error('Erro no pagamento:', error);
        res.status(500).send({
            error: error.message,
        });
    }
});

// Inicia o servidor
app.listen(3000, () => {
    console.log('Servidor rodando na porta 3000');
});