import Home from './Pages/Welcome.vue';

const routes = [
    { path: '/', name: 'home', component: Home },
    { path: '/login', name: 'login', component: () => import('./Pages/Auth/Login.vue') },
    { path: '/register', name: 'register', component: () => import('./Pages/Auth/Register.vue') },
    { path: '/forgot-password', name: 'forgot.password', component: () => import('./Pages/Auth/ForgotPassword.vue') },
    { path: '/reset-password', name: 'reset.password', component: () => import('./Pages/Auth/ResetPassword.vue') },
    { path: '/verify-email', name: 'verify.email', component: () => import('./Pages/Auth/VerifyEmail.vue') },
    { path: '/confirm-password', name: 'confirm.password', component: () => import('./Pages/Auth/ConfirmPassword.vue') },
    { path: '/event/registration', name: 'user.event.create', component: () => import('./Pages/User/Registration.vue') },
    { path: '/booked-events' , name: 'user.event.index', component: () => import('./Pages/User/List.vue') },
];

export default routes;