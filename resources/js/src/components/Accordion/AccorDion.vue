<template>
    <div>
        <button @click="this.isOpen = !this.isOpen;" class="w-full text-center px-4 py-2 border-b border-gray-300 bg-gray-100 hover:bg-gray-200">
            <h3 class="text-lg font-semibold">{{ title }}</h3>
        </button>
        <transition name="fade">
            <div v-show="isOpen" class="p-4">
                <slot></slot>
            </div>
        </transition>
    </div>
</template>

<script>
export default {
    name: 'Accordion',
    props: {
        title: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            isOpen: false
        };
    },

};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
}
.fade-enter, .fade-leave-to {
    opacity: 0;
}
</style>
