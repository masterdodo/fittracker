<template>
    <div>
        <h2>Leaderboard</h2>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th scope="col">User</th>
                    <th scope="col"># of activities</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(acts, user) in users" v-bind:key="user">
                    <td>{{ user }}</td>
                    <th scope="row">{{ acts }}</th>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            users: []
        };
    },
    mounted() {
        axios
            .get("api/leaderboard")
            .then(response => {
                this.users = response.data;
            })
            .catch(() => {
                this.$message({
                    type: "error",
                    message: "Problem loading the leaderboard"
                });
            });
    }
};
</script>
