<template>
    <nav
        class="block w-full max-w-screen-xl px-6 py-3 mx-auto text-white bg-white border shadow-md rounded-xl border-white/80 bg-opacity-80 backdrop-blur-2xl backdrop-saturate-200">
        <div class="flex items-center justify-between text-blue-gray-900">
            <router-link to="/"
                class="mr-4 block cursor-pointer py-1.5 font-sans text-base font-semibold leading-relaxed tracking-normal text-inherit antialiased"
                :class="{ 'active': $route.path === '/' }">
                Kel Engenharia
            </router-link>
            <div class="hidden lg:block">
                <ul class="flex flex-col gap-2 my-2 lg:mb-0 lg:mt-0 lg:flex-row lg:items-center lg:gap-6">
                    <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                        <router-link to="/" class="flex items-center transition-colors hover:text-gray-600"
                            :class="{ 'active': $route.path === '/' }">
                            DashBoard
                        </router-link>
                    </li>
                    <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                        <router-link to="/pedidos" class="flex items-center transition-colors hover:text-gray-600"
                            :class="{ 'active': $route.path === '/pedidos' }">
                            Pedidos
                        </router-link>
                    </li>
                    <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                        <router-link to="/produtos" class="flex items-center transition-colors hover:text-gray-600"
                            :class="{ 'active': $route.path === '/produtos' }">
                            Produtos
                        </router-link>
                    </li>
                    <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                        <router-link to="/clientes" class="flex items-center transition-colors hover:text-gray-600"
                            :class="{ 'active': $route.path === '/clientes' }">
                            Clientes
                        </router-link>
                    </li>
                    <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                        <router-link to="/funcionarios" class="flex items-center transition-colors hover:text-gray-600"
                            :class="{ 'active': $route.path === '/funcionarios' }">
                            Funcionarios
                        </router-link>
                    </li>
                    <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                        <router-link to="/fornecedor" class="flex items-center transition-colors hover:text-gray-600"
                            :class="{ 'active': $route.path === '/fornecedor' }">
                            Fornecedor
                        </router-link>
                    </li>
                    <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                        <router-link to="/marcas" class="flex items-center transition-colors hover:text-gray-600"
                            :class="{ 'active': $route.path === '/marcas' }">
                            Marcas
                        </router-link>
                    </li>
                    <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                        <router-link to="/compras" class="flex items-center transition-colors hover:text-gray-600"
                            :class="{ 'active': $route.path === '/compras' }">
                            Compras
                        </router-link>
                    </li>
                </ul>
            </div>
            <button @click="isMenuOpen = !isMenuOpen"
                class="relative ml-auto h-6 max-h-[40px] w-6 max-w-[40px] select-none rounded-lg text-center align-middle font-sans text-xs font-medium uppercase text-inherit transition-all hover:bg-transparent focus:bg-transparent active:bg-transparent disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none lg:hidden"
                type="button">
                <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" aria-hidden="true" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"></path>
                    </svg>
                </span>
            </button>
        </div>
        <div v-show="isMenuOpen" class="lg:hidden mt-2">
            <ul class="flex flex-col gap-2">
                <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                    <router-link to="/" class="flex items-center transition-colors hover:text-gray-600"
                        :class="{ 'active': $route.path === '/' }">
                        DashBoard
                    </router-link>
                </li>
                <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                    <router-link to="/pedidos" class="flex items-center transition-colors hover:text-gray-600"
                        :class="{ 'active': $route.path === '/pedidos' }">
                        Pedidos
                    </router-link>
                </li>
                <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                    <router-link to="/produtos" class="flex items-center transition-colors hover:text-gray-600"
                        :class="{ 'active': $route.path === '/produtos' }">
                        Produtos
                    </router-link>
                </li>
                <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                    <router-link to="/clientes" class="flex items-center transition-colors hover:text-gray-600"
                        :class="{ 'active': $route.path === '/clientes' }">
                        Clientes
                    </router-link>
                </li>
                <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                    <router-link to="/funcionarios" class="flex items-center transition-colors hover:text-gray-600"
                        :class="{ 'active': $route.path === '/funcionarios' }">
                        Funcionarios
                    </router-link>
                </li>
                <li class="block p-1 font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900">
                    <router-link to="/fornecedor" class="flex items-center transition-colors hover:text-gray-600"
                        :class="{ 'active': $route.path === '/fornecedor' }">
                        Fornecedor
                    </router-link>
                </li>
            </ul>
        </div>
    </nav>
</template>
<script>
export default {
    name: 'NavBar',
    data() {
        return {
            isMenuOpen: false, // Initial state for menu visibility
        };
    },
};
</script>
<style scoped>
/* Estilize a classe 'active' como desejar */
.active {
    font-weight: bold;
    /* Exemplo de estilo para item ativo */
    color: #000000;
    /* Exemplo de cor para item ativo */
}
</style>