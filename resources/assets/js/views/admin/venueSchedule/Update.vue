<template>
    <venue-schedule-form 
        :venueCourseForm="venueCourseForm"
        :course_times="course_times"
        :venue_schedules="venue_schedules"
    >
    </venue-schedule-form>
</template>

<script>
    import VenueScheduleForm from './Form';
    import { stack_error } from 'config/helper';
    export default {
        components: { VenueScheduleForm },
        data() {
            return {
                venueCourseForm: {},
                course_times : {},
                venue_schedules : {}
            }
        },
        created() {
            this.loadData();
        },

        methods : {
            loadData() 
            {
                var that = this, id = this.$route.params.id, url = 'venueSchedules/' + id + '/edit';
                that.$http({
                    method: "GET",
                    url: url
                })
                .then(function (response) {
                    var responseJson = response.data;
                    var data = responseJson.data;
                    that.venueCourseForm = data.venue_course;
                    that.course_times    = data.course_times;
                    that.venue_schedules = data.venue_schedules;
                })
                .catch(function (error) {
                    console.log('load  venue schedules error')
                    stack_error(error);
                }); 
            }

        }
    }

</script>