<script setup>
import PostItem from "@/Components/app/PostItem.vue";

const post1 = {
    user: {
        id: 1,
        avatar: 'https://randomuser.me/api/portraits/women/43.jpg',
        name: 'Jhon Smith'
    },
    group: null,
    attachments: [
        {
            id: 1,
            name: 'test.png',
            url: 'https://loremflickr.com/1000/1000',
            mime: 'image/png'
        },
        {
            id: 2,
            name: 'test2.png',
            url: 'https://loremflickr.com/1000/1000',
            mime: 'image/png'
        },
        {
            id: 3,
            name: 'MyDocument.png',
            url: '#',
            mime: 'application/msword'
        }
    ],
    body: `
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, assumenda consequuntur dolorem eius est eveniet harum itaque numquam quidem sit tempore voluptas! Eum fuga maxime nemo nobis optio placeat sit.</p>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, assumenda consequuntur dolorem eius est eveniet harum itaque numquam quidem sit tempore voluptas! Eum fuga maxime nemo nobis optio placeat sit.</p>
    `,
    created_at: '2023-11-19 15:12'
}

const post2 = {
    user: {
        id: 1,
        avatar: 'https://randomuser.me/api/portraits/women/53.jpg',
        name: 'James Black'
    },
    group: {
        id: 1,
        name: 'Разработчики Laravel'
    },
    body: `
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, assumenda consequuntur dolorem eius est eveniet harum itaque numquam quidem sit tempore voluptas! Eum fuga maxime nemo nobis optio placeat sit.</p>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, assumenda consequuntur dolorem eius est eveniet harum itaque numquam quidem sit tempore voluptas! Eum fuga maxime nemo nobis optio placeat sit.</p>
    `,
    created_at: '2023-11-19 15:12'
}
</script>

<template>
    <div>
        <PostItem :post="post1"/>
        <PostItem :post="post2"/>
    </div>
</template>

<style scoped>

</style>
