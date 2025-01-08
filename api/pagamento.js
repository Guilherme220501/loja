import Stripe from 'stripe';

// Inicialize a instância do Stripe com sua chave secreta
const stripe = Stripe('sk_live_...UT9D'); // Substitua pela sua chave secreta do Stripe

export default async function handler(req, res) {
    if (req.method === 'POST') {
        const { paymentMethodId, amount } = req.body; // Recebe o paymentMethodId e o valor total (em centavos)

        console.log('Requisição recebida no servidor');
        console.log('PaymentMethodId:', paymentMethodId);
        console.log('Amount:', amount);

        try {
            // Criar o PaymentIntent
            const paymentIntent = await stripe.paymentIntents.create({
                amount: amount, // Valor em centavos
                currency: 'brl', // Moeda em Reais
                payment_method: paymentMethodId, // paymentMethodId gerado no front-end
                confirmation_method: 'manual',
                confirm: true, // Confirma automaticamente a transação
            });

            console.log('PaymentIntent criado com sucesso:', paymentIntent.id);

            res.status(200).json({
                success: true,
                paymentIntentId: paymentIntent.id,
            });
        } catch (error) {
            console.error('Erro no pagamento:', error);
            res.status(500).json({
                error: error.message,
            });
        }
    } else {
        // Caso o método HTTP não seja POST
        res.status(405).json({ error: 'Método não permitido' });
    }
}