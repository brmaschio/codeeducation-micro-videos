import React from 'react';
import { BrowserRouter } from 'react-router-dom';

import AppRouter from './routes/AppRouter';

import './App.css';

import { Box, MuiThemeProvider, CssBaseline } from "@material-ui/core";

import { NavBar } from './components/NavBar/index';
import RouterBreadcrumbs from './components/Breadcrumbs';
import { SnackbarProvider } from "./components/SnackbarProvider";
import theme from './theme';

function App() {
  return (
    <div>

      <MuiThemeProvider theme={theme}>
        <SnackbarProvider>
          <CssBaseline />
          <BrowserRouter>
            <NavBar></NavBar>
            <Box paddingTop={'70px'}>
              <RouterBreadcrumbs />
              <AppRouter></AppRouter>
            </Box>
          </BrowserRouter>
        </SnackbarProvider>
      </MuiThemeProvider>

    </div>
  );
}

export default App;
