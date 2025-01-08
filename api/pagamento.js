import Stripe from 'stripe';

const stripe = Stripe(process.env.STRIPE_SECRET_KEY);

export default async function handler(req, res) {
    if (req.method === 'POST') {
        const { paymentMethodId, amount } = req.body;

        try {
            // Criar o PaymentIntent com o valor correto (em centavos)
            const paymentIntent = await stripe.paymentIntents.create({
                amount: amount, // Valor em centavos (com desconto aplicado)
                currency: 'brl',
                payment_method: paymentMethodId,
                confirmation_method: 'manual',
                confirm: true,
            });

            res.status(200).json({
                success: true,
                paymentIntentId: paymentIntent.id,
            });
        } catch (error) {
            console.error('Erro no pagamento:', error);
            res.status(500).json({ error: error.message });
        }
    } else {
        res.status(405).json({ error: 'Método não permitido' });
    }
}