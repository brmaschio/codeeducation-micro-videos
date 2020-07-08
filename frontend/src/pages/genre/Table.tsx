import * as React from 'react';
import { useEffect, useState } from "react";

import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { Chip } from '@material-ui/core';

import format from "date-fns/format";
import parseISO from "date-fns/parseISO";

import genreHttp from "../../util/http/genre-http";

const columnsDefinitions: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome",
    },
    {
        name: "categories",
        label: "Categorias",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return value.map(value => value.name).join(", ");
            }
        },
    },
    {
        name: "is_active",
        label: "Ativo?",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return value ? <Chip label="Sim" /> : <Chip label="Não" />
            }
        },
    },
    {
        name: "created_at",
        label: "Criado em",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return <span> {format(parseISO(value), 'dd/MM/yyyy')}</span>
            }
        },
    }
];

const Table = () => {

    const [data, setData] = useState([]);

    useEffect(() => {

        genreHttp.list().then(response => {
            setData(response.data.data);
        });

    }, []);

    return (
        <MUIDataTable data={data} title="Categorias" columns={columnsDefinitions} ></MUIDataTable>
    )

}


export default Table;