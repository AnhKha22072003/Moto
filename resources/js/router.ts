import { createRouter, createWebHistory } from "vue-router";
import Login from "./pages/Login.vue";
import MotorcycleList from "./pages/MotorcycleList.vue";
import MotorcycleCreate from "./pages/MotorcycleCreate.vue";
import MotorcycleUpdate from "./pages/MotorcycleUpdate.vue";
import BulkUpdate from "./pages/BulkUpdate.vue";

const routes = [
    { path: "/login", component: Login },
    { path: "/motorcycles-view", component: MotorcycleList },
    { path: "/motorcycles-create", component: MotorcycleCreate },
    {
        path: "/motorcycles/:id/edit",
        component: MotorcycleUpdate,
        name: "motorcycle.edit",
    },
    { path: "/bulk-view", component: BulkUpdate },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});
router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem("token") !== null;
    if (to.path !== "/login" && !isAuthenticated) {
        next("/login");
    } else {
        next();
    }
});