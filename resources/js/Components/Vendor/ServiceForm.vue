<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '../TextInput.vue';
import InputLabel from '../InputLabel.vue';

const form = useForm({
    name: "",
    description: "",
    price: 0.00
})

const submit = () => {
    form.post(route('service.store'), {
        onFinish: () => form.reset('name', 'description', 'price'),
    })

    console.log('Here')
}

</script>

<template>
    <form @submit.prevent="submit" class="p-5">
        <div class="mt-4">
            <InputLabel for="name" value="name" />

            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
        </div>

        <div class="mt-4">
            <InputLabel for="description" value="description" />

            <TextInput id="description" type="text" class="mt-1 block w-full" v-model="form.description" required />
        </div>

        <div class="mt-4">
            <InputLabel for="price" value="price" />


            <input type="number" id="price" class="mt-1 block w-full">
        </div>

        <div class="mt-4">
            <button class="bg-blue-500 px-2 py-1" type="submit">Add</button>
        </div>


    </form>
</template>

<style></style>
