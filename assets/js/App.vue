<template>
    <div>
        <h2>Helcome home</h2>
        <p class="error" v-if="errors">{{ errors }}</p>
        <form @submit="exchange">
            <select  name="from" id="from" v-model="from">
                <option v-for="(from, index) in from_currency" :key="index" :value="from">{{ from }}</option>
            </select>
            <select  name="to" id="to" v-model="to">
                <option v-for="(to, index) in to_currency" :key="index" :value="to">{{ to }}</option>
            </select>
            <button>Convertir</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>From Currency</th>
                    <th>To Currency</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(currency, index) in currencies" :key="index">
                    <td>{{ currency.from }}</td>
                    <td>{{ currency.to }}</td>
                    <td>{{ currency.amount }}</td>
                </tr>                        
            </tbody>
        </table>
    </div>
</template>
<script>
import axios from "axios";
export default {
    data: () => ({
        counter: 0,
        from_currency: [
            'USD',
            'PLN'
        ],
        to_currency: [
            'MXN',
            'ERN',
            'DZD',
            'CDF',
            'MAD',
            'SYP'
        ],
        from: null,
        to: null,
        currencies: {},
        errors: null
    }),
    created() {
        this.getCurrencies()
    },
    methods: {
        getCurrencies() {
            axios.get('/api/exchange')
                .then((response) => {
                    this.currencies = response.data;
                })
                .catch(error => {
                    console.log(error.response)
                });
        },
        exchange(e) {
            e.preventDefault();
            const headers = {
                'Content-Type': 'application/json',        
            }
            const data = {
                'from_currency': this.from,
                'to_currency': this.to
            };
            axios.post('/api/exchange', data, {
                    headers: headers
                })
                .then((response) => {
                    this.getCurrencies()
                })
                .catch(error => {
                    this.errors = error.response.data.status
                });
        }
    }
}
</script>