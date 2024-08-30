<template>
    <div class="flex justify-center max-h-[200px] overflow-y-auto w-full sm:w-[100%]">
        <div class="w-full">
            <table class="w-full text-sm text-left rtl:text-right text-black">
                <thead class="text-xs uppercase bg-gray-300 text-center text-black sticky top-0">
                    <tr>
                        <th v-for="(header, index) in headers" :key="index" class="px-2 py-2 border-b border-gray-400">
                            {{ header }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr v-for="(row, rowIndex) in body" :key="rowIndex" class="border-b border-gray-400 text-center">
                        <td v-for="(cell, cellIndex) in row" :key="cellIndex"
                            class="px-2 py-2 whitespace-nowrap text-[11px]">
                            {{ cell }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: 'NormalTable',
    props: {
        headers: {
            type: Array,
            required: true
        },
        body: {
            required: true
        }
    },
    mouted(){
        console.log(this.body)
    }
};
</script>

