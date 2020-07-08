import React, { useEffect } from 'react';

import { useForm } from "react-hook-form";

import { ButtonProps, TextField, Box, Button, makeStyles, Theme, FormControl, FormLabel, RadioGroup, FormControlLabel, Radio } from '@material-ui/core';
import castMemberHttp from '../../util/http/cast-member-http';

import { CastMembersTypes } from './Table';

const castMembersTypes = Object.entries(CastMembersTypes);

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

    const { register, handleSubmit, getValues, setValue, watch } = useForm({
        defaultValues: {
            type: 1
        }
    });

    useEffect(() => {

        register({ name: 'type' });

    }, [register])

    async function onSubmit(formData, event) {

        castMemberHttp.create(formData).then(response => {
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

            <FormControl component="fieldset">
                <FormLabel component={"legend"}>Tipo</FormLabel>
                <RadioGroup
                    row
                    name="type"
                    onChange={event => { setValue(event.target.name, Number(event.target.value)) }}
                    value={`${watch('type')}`}
                >

                    {
                        castMembersTypes.map((value, key) => {
                            return (
                                <FormControlLabel
                                    key={key}
                                    value={value[0]}
                                    label={value[1]}
                                    control={<Radio />}
                                />
                            )
                        })
                    }

                </RadioGroup>
            </FormControl>

            <Box dir={"rtl"}>
                <Button onClick={() => onSubmit(getValues(), null)} {...buttonProps} >Salvar</Button>
                <Button type="submit" {...buttonProps} >Salvar e Continuar</Button>
            </Box>

        </form>
    );
};

export default Form;