<template>
   <!--  <div class="dashboard-editor-container">
        <div>
      		<img class='emptyGif' :src="emptyGif">
    	</div>
    </div> -->
    <div class="row">
    	<div class="row">
    		<section class="content">
			
        		<!-- /.box -->
    			<div class="col-md-3 col-sm-6 col-xs-12">
		          <div class="info-box">
		             <router-link :to="{path:'/admin/student/index'}">
                      	<span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                    </router-link> 

		            <div class="info-box-content">
		              <br>
		              <span class="info-box-text">学生总数</span>
		              <span class="info-box-number">{{statistics.students||0}}</span>
		            </div>
		            <!-- /.info-box-content -->
		          </div>
          		<!-- /.info-box -->
        		</div>

        		<div class="col-md-3 col-sm-6 col-xs-12">
		          <div class="info-box">
		          	<router-link :to="{path:'/admin/class/index'}">
                      	<span class="info-box-icon bg-green"><i class="fa fa-flag-o"></i></span>
                    </router-link> 
		            <div class="info-box-content">
		              <br>
		              <span class="info-box-text">道馆班级总数</span>
		              <span class="info-box-number">{{statistics.classes||0}}</span>
		            </div>
           		 	<!-- /.info-box-content -->
          			</div>
          		<!-- /.info-box -->
          		</div>

          		<div class="col-md-3 col-sm-6 col-xs-12">
		          	<div class="info-box">
		          		<router-link :to="{path:'/admin/card/index'}">
                      		<span class="info-box-icon bg-yellow"><i class="fa fa-files-o"></i></span>
                        </router-link> 
			            <div class="info-box-content">
			              <br>
			              <span class="info-box-text">道馆卡券数</span>
			              <span class="info-box-number">{{statistics.cards||0}}</span>
			            </div>
            		<!-- /.info-box-content -->
          			</div>
          		<!-- /.info-box -->
          		</div>
				
			     <div class="col-md-3 col-sm-6 col-xs-12">
		          <div class="info-box">
		          <!-- 	<router-link :to="{path:'/admin/card/index'}">
                    </router-link>  -->
                    <span class="info-box-icon bg-red"><i class="fa fa-star-o"></i></span>
		            
		            <div class="info-box-content">
		              <br>
		              <span class="info-box-text">管理道馆数</span>
		              <span class="info-box-number">{{statistics.venues||0}}</span>
		            </div>
		            <!-- /.info-box-content -->
		          </div>
		          <!-- /.info-box -->
		        </div>
		        <!-- /.col -->
          		


        
    		</section>
    		 
    	</div>
    </div>
</template>

<script>
import {stack_error} from 'config/helper';
export default {
    name : 'index',
    data () {
      	return  {
        	statistics: {},
            emptyGif: 'https://wpimg.wallstcn.com/0e03b7da-db9e-4819-ba10-9016ddfdaed3'
        }

    },
    created() {
    	this.getStatistics();
    },
    methods : {
    	getStatistics() 
    	{
    		var that = this;
            var url = '/statistics';
            this.$http({
                method :'GET',
                url : url
           })
           .then(function(response) 
           {
                let {data} = response;
                that.statistics = data.data; 
             })
            .catch(function(error) {
                stack_error(error);
            });

    	}

    }
}
</script>

<style lang="scss">
	.emptyGif {
		display: block;
		width: 45%;
		margin: 0 auto;
	}

	.dashboard-editor-container {
		background-color: #e3e3e3;
		min-height: 100vh;
		margin-top: -50px;
		padding: 100px 60px 0px;
		.pan-info-roles {
			font-size: 12px;
			font-weight: 700;
			color: #333;
			display: block;
		}
		.info-container {
			position: relative;
			margin-left: 190px;
			height: 150px;
			line-height: 200px;
			.display_name {
				font-size: 48px;
				line-height: 48px;
				color: #212121;
				position: absolute;
				top: 25px;
			}
		}
	}

</style>