import React, { useState } from 'react';
import { Link } from 'react-router-dom';

import { IconButton, Menu as MuiMenu, MenuItem } from "@material-ui/core";
import MenuIcon from "@material-ui/icons/Menu";

import routes, { MyRouteProps } from '../../routes';

const listRoutes = [
    'dashboard', 
    'categories.list',
    'cast_members.list',
    'genres.list',
];

export const Menu = () => {

    const menuRoutes = routes.filter(route => listRoutes.includes(route.name));

    const [anchorEl, setAnchorEl] = useState(null);
    const open = Boolean(anchorEl);
    const handleOpen = (event: any) => setAnchorEl(event.currentTarget);
    const handleClose = () => setAnchorEl(null);

    return (
        <React.Fragment>
            <IconButton color="inherit" aria-label="open drawer" aria-controls="menu-appbar"
                aria-haspopup="true" onClick={handleOpen}>
                <MenuIcon />
            </IconButton>

            <MuiMenu id="menu-appbar"
                onClose={handleClose}
                open={open}
                anchorEl={anchorEl}
                anchorOrigin={{ vertical: 'bottom', horizontal: 'center' }}
                transformOrigin={{ vertical: 'top', horizontal: 'center' }}
                getContentAnchorEl={null}
            >
                {
                    listRoutes.map((routeName, key) => {
                        const route = menuRoutes.find(route => route.name === routeName) as MyRouteProps;
                        return (
                            <MenuItem onClick={handleClose} key={key} component={Link} to={route.path as string}>
                                {route.label}
                            </MenuItem>
                        )
                    })
                }
            </MuiMenu>
        </React.Fragment>
    );
};