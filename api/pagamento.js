import Stripe from 'stripe';

const qrcode = require('qrcode');
const PIX = require('pix-utils'); // Biblioteca para gerar payload PIX

app.post('/api/gerar-pix', async (req, res) => {
    const { amount, key } = req.body; // Valor total e chave PIX
    try {
        const payload = PIX.static({
            key: key, // Sua chave PIX
            amount: amount / 100, // Valor em reais
            name: "Guilherme Barbosa Silva", // Nome do recebedor
            city: "Sao Paulo", // Cidade do recebedor
        });

        const qrCode = await qrcode.toDataURL(payload); // Gerar QR Code
        res.status(200).json({ qrCode });
    } catch (error) {
        console.error('Erro ao gerar PIX:', error);
        res.status(500).json({ error: error.message });
    }
});
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
                    enabled: true,  // Habilita métodos de pagamento automáticos
                    allow_redirects: 'never',  // Impede o uso de métodos que exigem redirecionamento
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
