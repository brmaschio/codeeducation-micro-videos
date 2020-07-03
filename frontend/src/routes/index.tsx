import { RouteProps } from "react-router-dom";

import Dashboard from "../pages/Dashboard";
import CategoryList from "../pages/category/PageList";
import CastMembersList from "../pages/cast-member/PageList";
import GenresList from '../pages/genre/PageList';

export interface MyRouteProps extends RouteProps {
    name: string;
    label: string;
};

const routes: MyRouteProps[] = [
    { name: 'cast_members.list', label: 'Listar Membros Do Elenco', path: '/cast_members', component: CastMembersList, exact: true },
    { name: 'cast_members.create', label: 'Criar Membros Do Elenco', path: '/cast_members/create', component: CastMembersList, exact: true },
    { name: 'categories.list', label: 'Listar Categorias', path: '/categories', component: CategoryList, exact: true },
    { name: 'categories.create', label: 'Criar Categorias', path: '/categories/create', component: CategoryList, exact: true },
    { name: 'dashboard', label: 'Dashboard', path: '/', component: Dashboard, exact: true },
    { name: 'genres.list', label: 'Listar Gêneros', path: '/genres', component: GenresList, exact: true },
    { name: 'genres.create', label: 'Criar Gêneros', path: '/genres/create', component: GenresList, exact: true },
];

export default routes;