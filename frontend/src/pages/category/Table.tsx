import * as React from 'react';
import { useEffect, useState } from "react";

import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';

import format from "date-fns/format";
import parseISO from "date-fns/parseISO";

import categoryHttp from '../../util/http/category-http';
import { BadgeYes, BadgeNo } from '../../components/Badge';

const columnsDefinitions: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome",
    },
    {
        name: "is_active",
        label: "Ativo?",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return value ? <BadgeYes /> : <BadgeNo />
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

        categoryHttp.list().then(response => {
            setData(response.data.data);
        });

    }, []);

    return (
        <MUIDataTable data={data} title="Categorias" columns={columnsDefinitions} ></MUIDataTable>
    )

}


export default Table;