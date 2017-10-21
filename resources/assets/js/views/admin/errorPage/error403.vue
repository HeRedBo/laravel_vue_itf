<template>
   <div class="error-page">
         <h2 class="headline text-yellow">403</h2>

         <div class="error-content" style="padding-top: 30px">
             <h3><i class="fa fa-warning text-yellow"></i>没有权限。</h3>

             <p>
                 没有权限.
                 此时你可以返回 <router-link to="/admin/dashboard">回首页</router-link> 或者返回 <a @click="$router.back();">上一页</a>
             </p>
         </div>
     </div>
</template>

<script>
import errorGif from 'images/401_images/401.gif';

export default {
    data() {
       return  {
        errorGif : errorGif + new Date()
       }
    },
    methods : {
        back() {
            if (this.$route.query.noGoBack) {
                this.$router.push({ path: '/dashboard' })
            } else {
                this.$router.go(-1)
            }
        }
    }
}
</script>


<style rel="stylesheet/scss" lang="scss" scoped>
    .errPage-container {
      width: 800px;
      margin: 100px auto;
      .pan-back-btn {
        background: #008489;
        color: #fff;
      }
      .pan-gif {
        margin: 0 auto;
        display: block;
      }
      .pan-img {
        display: block;
        margin: 0 auto;
      }
      .text-jumbo {
        font-size: 60px;
        font-weight: 700;
        color: #484848;
      }
      .list-unstyled {
        font-size: 14px;
        li {
          padding-bottom: 5px;
        }
        a {
          color: #008489;
          text-decoration: none;
          &:hover {
            text-decoration: underline;
          }
        }
      }
    }

    a {
        color: #008489;
        text-decoration: none;
        &:hover {
            text-decoration: underline;
          }
    }
  </style>
  