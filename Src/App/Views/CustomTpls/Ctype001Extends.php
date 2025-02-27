<template id="tpl-items-list-component">
    <div class="p-1">

        <h3> Items </h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in value">
                    <td> {{ item.item }} </td>
                    <td> {{ item.qty | formatDecimal }} </td>
                    <td> {{ item.price | formatDecimal }} </td>
                    <td> {{ item.price * item.qty | formatDecimal }} </td>
                </tr>
            <tfoot>
                <tr>
                    <td> {{ value.length }} </td>
                    <td> {{ sumOfQty | formatDecimal }} </td>
                    <td> </td>
                    <td>{{ sumOfTotal | formatDecimal}} </td>
                </tr>
            </tfoot>
            </tbody>
        </table>

    </div>
</template>

<script>
    Vue.component('items-list-component', {
        template: '#tpl-items-list-component',
        props: ['value'],
        data() {
            return {

            }
        },
        computed: {
            sumOfTotal: function() {
                sum = 0;
                this.value.forEach((item) => {
                    sum += item.qty * item.price;
                });

                return sum;
            },
            sumOfQty: function() {
                sum = 0;
                this.value.forEach((item) => {
                    sum += item.qty;
                });

                return sum;
            }
        }
    })
</script>