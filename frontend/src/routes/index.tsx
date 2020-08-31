import { RouteProps } from "react-router-dom";

import Dashboard from "../pages/Dashboard";

import UploadPage from '../pages/uploads/index';

import CategoryList from "../pages/category/PageList";
import CategoryForm from "../pages/category/PageForm";

import CastMembersList from "../pages/cast-member/PageList";
import CastMembersForm from "../pages/cast-member/PageForm";

import GenresList from '../pages/genre/PageList';
import GenreForm from "../pages/genre/PageForm";

import VideoList from '../pages/video/PageList';
import VideoForm from '../pages/video/PageForm';

export interface MyRouteProps extends RouteProps {
    name: string;
    label: string;
};

const routes: MyRouteProps[] = [

    { name: 'dashboard', label: 'Dashboard', path: '/', component: Dashboard, exact: true },
    { name: 'uploads', label: 'Uploads', path: '/uploads', component: UploadPage, exact: true },

    { name: 'cast_members.list', label: 'Listar Membros Do Elenco', path: '/cast_members', component: CastMembersList, exact: true },
    { name: 'cast_members.create', label: 'Criar Membros Do Elenco', path: '/cast_members/create', component: CastMembersForm, exact: true },
    { name: 'cast_members.edit', label: 'Editar Membros Do Elenco', path: '/cast_members/:id/edit', component: CastMembersForm, exact: true },

    { name: 'categories.list', label: 'Listar Categorias', path: '/categories', component: CategoryList, exact: true },
    { name: 'categories.create', label: 'Criar Categorias', path: '/categories/create', component: CategoryForm, exact: true },
    { name: 'categories.edit', label: 'Editar categoria', path: '/categories/:id/edit', component: CategoryForm, exact: true },

    { name: 'genres.list', label: 'Listar Gêneros', path: '/genres', component: GenresList, exact: true },
    { name: 'genres.create', label: 'Criar Gêneros', path: '/genres/create', component: GenreForm, exact: true },
    { name: 'genres.edit', label: 'Editar Gêneros', path: '/genres/:id/edit', component: GenreForm, exact: true },

    { name: 'videos.list', label: 'Listar videos', path: '/videos', component: VideoList, exact: true },
    { name: 'videos.create', label: 'Criar videos', path: '/videos/create', component: VideoForm, exact: true },
    { name: 'videos.edit', label: 'Editar videos', path: '/videos/:id/edit', component: VideoForm, exact: true }

];

export default routes;