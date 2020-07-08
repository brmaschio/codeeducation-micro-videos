import React, { useState, useEffect } from 'react';

import { useForm } from "react-hook-form";

import { ButtonProps, TextField, Checkbox, Box, Button, makeStyles, Theme, FormControl, InputLabel, Select, MenuItem } from '@material-ui/core';
import genreHttp from '../../util/http/genre-http';
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

    const [categories, setCategories] = useState([]);

    const { register, handleSubmit, getValues, watch, setValue } = useForm({
        defaultValues: {
            is_active: true,
            categories_id: []
        }
    });

    useEffect(() => {

        categoryHttp.list().then(response => {
            setCategories(response.data.data)
        });

    }, []);

    useEffect(() => {

        register({ name: 'categories_id' });

    }, [register])

    function onSubmit(formData, event) {

        console.log(formData)

        genreHttp.create(formData).then(response => {
            console.log(response.data, event);
        });

    }

    const onChangeCategory = (event) => {
        setValue(event.target.name, [event.target.value]);
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


            <FormControl>
                <InputLabel id="demo-simple-select-label">Categorias</InputLabel>
                <Select
                    labelId="categories_id"
                    id="categories_id"
                    name="categories_id"
                    variant={"outlined"}
                    value={`${watch('categories_id')}`}
                    onChange={event => onChangeCategory(event)}
                // multiple
                >
                    {
                        categories.map((value: any, key) => {
                            return (
                                <MenuItem key={key} value={value.id}>{value.name}</MenuItem>
                            )
                        })
                    }

                </Select>
            </FormControl>

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