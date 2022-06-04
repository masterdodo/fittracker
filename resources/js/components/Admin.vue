<template>
    <div>
        <h2 class="text-center">Users</h2>
        <table class="table table-hover text-center">
            <thead>
                <tr class="table-info">
                    <th scope="col">id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Creation date</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" v-bind:key="user.id">
                    <th scope="row">{{ user.id }}</th>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ timeAgo.format(new Date(user.created_at)) }}</td>
                    <td>
                        <el-tooltip
                            class="item"
                            effect="dark"
                            content="Delete all routines from the user"
                            placement="top"
                        >
                            <el-button
                                icon="el-icon-delete"
                                type="danger"
                                @click="confirmRoutineDeletion(user.id)"
                                round
                                >R</el-button
                            >
                        </el-tooltip>
                        <el-tooltip
                            class="item"
                            effect="dark"
                            content="Delete all activities from the user"
                            placement="top"
                        >
                            <el-button
                                icon="el-icon-delete"
                                type="danger"
                                @click="confirmActivityDeletion(user.id)"
                                round
                                >A</el-button
                            >
                        </el-tooltip>
                        <el-tooltip
                            class="item"
                            effect="dark"
                            content="Delete the entire user account"
                            placement="top"
                        >
                            <el-button
                                icon="el-icon-delete"
                                type="danger"
                                @click="confirmUserDeletion(user.id)"
                                round
                                >USER</el-button
                            >
                        </el-tooltip>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import TimeAgo from "javascript-time-ago";
import en from "javascript-time-ago/locale/en";
import axios from "axios";

TimeAgo.addDefaultLocale(en);
export default {
    data() {
        return {
            timeAgo: new TimeAgo("en-US"),
            users: []
        };
    },
    methods: {
        deleteRoutines(id) {
            axios
                .get("api/admin/routines/delete/" + id)
                .then(() => {
                    this.$message({
                        type: "success",
                        message:
                            "All routines for the user successfully deleted"
                    });
                })
                .catch(error => {
                    this.$message({
                        type: "error",
                        message: error.response.data.message
                    });
                });
        },
        deleteActivities(id) {
            axios
                .get("api/admin/activities/delete/" + id)
                .then(() => {
                    this.$message({
                        type: "success",
                        message:
                            "All activities for the user successfully deleted"
                    });
                })
                .catch(error => {
                    this.$message({
                        type: "error",
                        message: error.response.data.message
                    });
                });
        },
        deleteUser(id) {
            axios
                .get("api/admin/users/delete/" + id)
                .then(() => {
                    this.$message({
                        type: "success",
                        message: "User successfully deleted"
                    });
                    axios
                        .get("api/users/all")
                        .then(response => {
                            this.users = response.data;
                        })
                        .catch(error => {
                            this.$message({
                                type: "success",
                                message: error.response.data.message
                            });
                        });
                })
                .catch(error => {
                    this.$message({
                        type: "error",
                        message: error.response.data.message
                    });
                });
        },
        confirmRoutineDeletion(id) {
            this.$confirm(
                "This will permanently delete all of user's routines. Continue?",
                "Warning",
                {
                    confirmButtonText: "OK",
                    cancelButtonText: "Cancel",
                    type: "warning"
                }
            )
                .then(() => {
                    this.deleteRoutines(id);
                })
                .catch(() => {
                    this.$message({
                        type: "info",
                        message: "Delete canceled"
                    });
                });
        },
        confirmActivityDeletion(id) {
            this.$confirm(
                "This will permanently delete all of user's activities. Continue?",
                "Warning",
                {
                    confirmButtonText: "OK",
                    cancelButtonText: "Cancel",
                    type: "warning"
                }
            )
                .then(() => {
                    this.deleteActivities(id);
                })
                .catch(() => {
                    this.$message({
                        type: "info",
                        message: "Delete canceled"
                    });
                });
        },
        confirmUserDeletion(id) {
            this.$confirm(
                "This will permanently delete the user. Continue?",
                "Warning",
                {
                    confirmButtonText: "OK",
                    cancelButtonText: "Cancel",
                    type: "warning"
                }
            )
                .then(() => {
                    this.deleteUser(id);
                })
                .catch(() => {
                    this.$message({
                        type: "info",
                        message: "Delete canceled"
                    });
                });
        }
    },
    mounted() {
        this.timeAgo = new TimeAgo("en-US");
        axios
            .get("api/users/all")
            .then(response => {
                this.users = response.data;
            })
            .catch(error => {
                this.$message({
                    type: "error",
                    message: error.response.data.message
                });
            });
    }
};
</script>
