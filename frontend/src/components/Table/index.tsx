import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn, MUIDataTableOptions, MUIDataTableProps } from "mui-datatables";
import { merge, omit, cloneDeep } from 'lodash';
import useTheme from "@material-ui/core/styles/useTheme";
import { Theme, MuiThemeProvider, useMediaQuery } from "@material-ui/core";
import DebouncedTableSearch from "./DebouncedTableSearch";

const makeDefaultOptions = (debouncedSearchTime?): MUIDataTableOptions => ({
    print: false,
    download: false,
    textLabels: {
        body: {
            noMatch: "Nenhum registro encontrado",
            toolTip: "Classificar",
        },
        pagination: {
            next: "Próxima página",
            previous: "Página anterior",
            rowsPerPage: "Por página:",
            displayRows: "de",
        },
        toolbar: {
            search: "Busca",
            downloadCsv: "Download CSV",
            print: "Imprimir",
            viewColumns: "Ver Colunas",
            filterTable: "Filtrar Tabelas",
        },
        filter: {
            all: "Todos",
            title: "FILTROS",
            reset: "LIMPAR",
        },
        viewColumns: {
            title: "Ver Colunas",
            titleAria: "Ver/Esconder Colunas da Tabela",
        },
        selectedRows: {
            text: "registro(s) selecionados",
            delete: "Excluir",
            deleteAria: "Excluir registros selecionados",
        }
    },
    customSearchRender: (searchText: string,
        handleSearch: any,
        hideSearch: any,
        options: any) => {
        return <DebouncedTableSearch
            searchText={searchText}
            onSearch={handleSearch}
            onHide={hideSearch}
            options={options}
            debouceTime={debouncedSearchTime}
        />
    }
});

export interface TableColumn extends MUIDataTableColumn {
    width?: string
}
interface TableProps extends MUIDataTableProps {
    columns: TableColumn[];
    loading?: boolean;
    debouncedSearchTime?: number;
}

export interface MuiDataTableRefComponent {
    changePage: (page: number) => void;
    changeRowsPerPage: (rowsPerPage: number) => void;
}

const Table = React.forwardRef<MuiDataTableRefComponent, TableProps>((props, ref) => {
    function extractMuiDataTableColumns(columns: TableColumn[]): MUIDataTableColumn[] {
        setColumnsWidth(columns);
        return columns.map(column => omit(column, 'width'))
    }

    function setColumnsWidth(columns: TableColumn[]) {
        columns.forEach((column, key) => {

            // if(column.width) {
            //     const overrides = theme.overrides as any;
            //     overrides.MUIDataTableHeadCell.fixedHeaderCommon[`&:nth-child(${key + 2})`] = {
            //         width: column.width
            //     }
            // }
        })
    }

    function applyLoading() {
        const textLabels = newProps.options?.textLabels as any;
        textLabels.body.noMatch = newProps.loading === true
            ? 'Carregando...'
            : textLabels.body.noMatch;

    }

    function applyResponsive() {
        newProps.options.responsive = isSmOrDown ? 'scrollMaxHeight' : 'stacked';
    }

    function getOriginalMuiDataTableProps() {
        return {
            ...omit(newProps, 'loading'),
            ref
        };
    }


    const theme = cloneDeep<Theme>(useTheme());
    const isSmOrDown = useMediaQuery(theme.breakpoints.down('sm'));


    const defaultOptions = makeDefaultOptions(props.debouncedSearchTime);
    const newProps = merge(
        { options: cloneDeep(defaultOptions) },
        props,
        { columns: extractMuiDataTableColumns(props.columns) }
    );

    applyLoading();
    applyResponsive();

    const originalProps = getOriginalMuiDataTableProps();

    return (
        <MuiThemeProvider theme={theme}>
            <MUIDataTable {...originalProps} />
        </MuiThemeProvider>
    );
});


export default Table;