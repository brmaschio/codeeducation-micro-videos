import React from 'react';

import { useForm } from "react-hook-form";

import { ButtonProps, TextField, Checkbox, Box, Button, makeStyles, Theme } from '@material-ui/core';
import categoryHttp from '../../util/http/category-http';

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

const Form = () => {

    const classes = useStyles();

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: 'outlined'
    }

    const { register, handleSubmit, getValues } = useForm({
        defaultValues: {
            is_active: true
        }
    });

    function onSubmit(formData, event) {

        categoryHttp.create(formData).then(response => {
            console.log(response.data, event);
        });

    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>

            <TextField
                inputRef={register}
                name="name"
                label="Nome"
                fullWidth
                variant={"outlined"}
            />

            <TextField
                inputRef={register}
                name="description"
                label="Descrição"
                multiline
                rows="4"
                fullWidth
                variant={"outlined"}
                margin={"normal"}
            />

            <Checkbox
                name={"is_active"}
                inputRef={register}
                defaultChecked
            />
            Ativo ?

            <Box dir={"rtl"}>
                <Button onClick={() => onSubmit(getValues(), null)} {...buttonProps} >Salvar</Button>
                <Button type="submit" {...buttonProps} >Salvar e Continuar</Button>
            </Box>

        </form>
    );
};

export default Form;