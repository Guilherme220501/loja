import Stripe from 'stripe';

const stripe = Stripe(process.env.STRIPE_SECRET_KEY);  // A chave secreta será lida do .env

export default async function handler(req, res) {
    if (req.method === 'POST') {
        const { paymentMethodId, amount } = req.body; // Receber o valor com desconto

        try {
            // Criar o PaymentIntent com o valor correto (em centavos)
            const paymentIntent = await stripe.paymentIntents.create({
                amount: amount, // Valor em centavos (com desconto aplicado)
                currency: 'brl',
                payment_method: paymentMethodId, // O ID do método de pagamento
                automatic_payment_methods: {
                    enabled: true, // Habilita métodos de pagamento automáticos
                },
                confirm: true,  // Confirma o pagamento imediatamente
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