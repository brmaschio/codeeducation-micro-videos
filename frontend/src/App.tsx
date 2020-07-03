import React from 'react';
import { BrowserRouter } from 'react-router-dom';

import AppRouter from './routes/AppRouter';

import './App.css';

import { Box } from "@material-ui/core";

import { NavBar } from './components/NavBar/index';
import RouterBreadcrumbs from './components/Breadcrumbs';

function App() {
  return (
    <div>

      <BrowserRouter>
        <NavBar></NavBar>
        <Box paddingTop={'70px'}>
          <RouterBreadcrumbs />
          <AppRouter></AppRouter>
        </Box>
      </BrowserRouter>

    </div>
  );
}

export default App;
