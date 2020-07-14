import * as React from 'react';
import { Link } from "react-router-dom";

import { Box, Fab } from "@material-ui/core";
import AddIcon from '@material-ui/icons/Add';

import { Page } from '../../components/Page';

import Table from './Table';

const List = () => {
    return (
        <Page title="Listar Categorias">

            <Box dir={'rtl'} paddingBottom={2}>
                <Fab title="Adicionar categoria" size="small" component={Link} to="/categories/create"
                    color="secondary">
                    <AddIcon />
                </Fab>
            </Box>

            <Box>
                <Table></Table>
            </Box>

        </Page>
    );
};

export default List;