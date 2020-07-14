import React, { useEffect, useState } from 'react';
import { useParams, useHistory } from 'react-router-dom';
import { useForm } from "react-hook-form";
import { ButtonProps, TextField, Box, Button, makeStyles, Theme, FormControl, FormLabel, RadioGroup, FormControlLabel, Radio, FormHelperText } from '@material-ui/core';
import castMemberHttp from '../../util/http/cast-member-http';
import { CastMembersTypes } from './Table';
import { useSnackbar } from "notistack";

import * as yup from '../../util/vendor/yup';

const castMembersTypes = Object.entries(CastMembersTypes);

const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

const validationSchema = yup.object().shape({
    name: yup.string().label('Nome').required().max(255),
    type: yup.number().label('Tipo').required()
});

const Form = () => {

    const classes = useStyles();

    const { enqueueSnackbar } = useSnackbar();
    const { id } = useParams();
    const history = useHistory();
    const [castMember, setCastMember] = useState<{ id: string } | null>(null);
    const [loading, setLoading] = useState(false);

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: 'contained',
        color: 'secondary'
    }

    const { register, handleSubmit, getValues, setValue, watch, reset, errors } = useForm({
        
        // AGUARDANDO AJUDA DO FORUM
        // validationSchema,

        defaultValues: {
            type: 1
        }
    });

    async function onSubmit(formData, event) {

        setLoading(true);

        const http = !castMember
            ? castMemberHttp.create(formData)
            : castMemberHttp.update(castMember.id, formData);

        http.then(({ data }) => {

            enqueueSnackbar('Salvo com sucesso!', { variant: "success" });

            setTimeout(() => {

                console.log(data.data);

                event ? (id
                    ? history.replace(`/cast_members/${data.data.id}/edit`)
                    : history.push(`/cast_members/${data.data.id}/edit`)
                )
                    : history.push('/cast_members');

            })
        }).catch(error => { 
            console.log(error)
            enqueueSnackbar('Erro Ao Processar Serviço Remoto', {variant: "error"});
        }).finally(() => setLoading(false));

    }

    useEffect(() => {

        register({ name: 'type' });

    }, [register])

    useEffect(() => {

        if (!id) {
            return;
        }

        setLoading(true);

        castMemberHttp.get(id).then(({ data }) => {
            setCastMember(data.data);
            reset(data.data);
        }).catch(error => { 
            console.log(error)
            enqueueSnackbar('Erro Ao Processar Serviço Remoto', {variant: "error"});
        }).finally(() => setLoading(false));

    }, [id, reset, enqueueSnackbar]);

    return (
        <form onSubmit={handleSubmit(onSubmit)}>

            <TextField
                inputRef={register}
                name="name"
                label="Nome"
                fullWidth
                variant={"outlined"}
                error={(errors as any).name !== undefined}
                helperText={(errors as any).name && (errors as any).name.message}
                InputLabelProps={{ shrink: true }}
                disabled={loading}
            />

            <FormControl
                component="fieldset"
                margin={"normal"}
                error={(errors.type) !== undefined}
                disabled={loading}>

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
                {
                    (errors as any).type &&
                    <FormHelperText id="type-helper-text">{(errors as any).type.message}</FormHelperText>
                }
            </FormControl>

            <Box dir={"rtl"}>
                <Button color="primary" onClick={() => onSubmit(getValues(), null)} {...buttonProps} >Salvar</Button>
                <Button type="submit" {...buttonProps} >Salvar e Continuar</Button>
            </Box>

        </form>
    );
};

export default Form;