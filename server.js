const express = require('express');
const stripe = require('stripe')('sk_live_...UT9D'); // Substitua pela sua chave secreta real
const app = express();

// Middleware para analisar JSON
app.use(express.json());

// Endpoint para processar pagamento
app.post('/pagamento', async (req, res) => {
    const { paymentMethodId, items } = req.body; // Recebendo paymentMethodId e items do frontend

    try {
        // Recalcular o valor no backend para evitar fraudes
        const amount = items.reduce((total, item) => total + item.price * item.quantity, 0) * 100; // Valor em centavos

        const pagamento = await stripe.paymentIntents.create({
            amount,
            currency: 'brl',
            payment_method: paymentMethodId,
            confirm: true, // Confirmar automaticamente
        });

        res.status(200).send({
            success: true,
            paymentIntentId: pagamento.id,
        });
    } catch (error) {
        console.error('Erro no pagamento:', error.message);
        res.status(500).send({
            error: error.message,
        });
    }
});

// Iniciar o servidor
app.listen(3000, () => {
    console.log('Servidor rodando na porta 3000');
});