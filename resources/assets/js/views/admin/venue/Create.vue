<template>
        <venue-form :venueForm="venueForm" :select="select" :image = "image"></venue-form>	
</template>

<script>
import VenueForm from './Form';
export default {
	components: {VenueForm},
    data() {
        return {
           venueForm : {
               parent_id: 0
            },
            select: {},
            image: 'https://itftkd-test-1252493044.cos.ap-guangzhou.myqcloud.com/files/avatar/default.png',
        }
    },
    created() {   
    },
    methods : {
    }
}
</script>