<template>
    <div class="w-full text-center mt-2">
        <div class="relative flex items-center justify-center">
            <button @click="toggleOpen" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 me-2 mb-2 focus:outline-none ">
                <h3 class="text-lg">{{ title }}</h3>
            </button>
        </div>
        <transition name="fade" @before-enter="beforeEnter" @enter="enter" @leave="leave">
            <div v-show="isOpen" class="accordion-content mb-2" ref="content">
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
    methods: {
        toggleOpen() {
            this.isOpen = !this.isOpen;
        },
        beforeEnter(el) {
            el.style.height = '0px';
            el.style.opacity = '0';
            el.style.overflow = 'hidden';  // Ensure content does not overflow
        },
        enter(el) {
            el.style.transition = 'height 0.5s ease, opacity 0.5s ease';
            el.style.height = `${el.scrollHeight}px`;
            el.style.opacity = '1';
        },
        leave(el) {
            el.style.transition = 'height 0.5s ease, opacity 0.5s ease';
            el.style.height = '0px';
            el.style.opacity = '0';
        }
    }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
}
.accordion-content {
    overflow: hidden;
    transition: height 0.5s ease, opacity 0.5s ease;
}

</style>
