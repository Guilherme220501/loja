const express = require('express');
const stripe = require('stripe')('sk_live_...UT9D'); // Substitua pela sua chave secreta real
const app = express();

// Middleware para analisar JSON
app.use(express.json());

// Endpoint para processar pagamento
app.post('/pagamento', async (req, res) => {
    const { paymentMethodId, items } = req.body;

    // Log para verificar os dados recebidos
    console.log('Dados recebidos no backend:', { paymentMethodId, items });

    try {
        // Calcular o valor total
        const amount = items.reduce((total, item) => total + item.price * item.quantity, 0) * 100;

        // Criar Payment Intent
        const pagamento = await stripe.paymentIntents.create({
            amount,
            currency: 'brl',
            payment_method: paymentMethodId,
            confirm: true, // Confirma automaticamente
        });

        console.log('Pagamento criado com sucesso:', pagamento);

        res.status(200).send({
            success: true,
            paymentIntentId: pagamento.id,
        });
    } catch (error) {
        console.error('Erro ao processar pagamento:', error.message);

        res.status(500).send({
            error: error.message,
        });
    }
});

// Iniciar o servidor
app.listen(3000, () => {
    console.log('Servidor rodando na porta 3000');
});