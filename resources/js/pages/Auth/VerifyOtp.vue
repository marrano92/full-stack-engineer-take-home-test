<template>
    <AuthBase title="Enter verification code">
        <Head title="Verify Code" />

        <div class="mb-4 text-center text-sm text-gray-600">
            We sent a verification code to:
            <div class="font-semibold text-gray-900">{{ email }}</div>
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="otp_code" class="text-xs text-label uppercase">Verification Code</Label>
                    <Input
                        id="otp_code"
                        type="text"
                        required
                        autofocus
                        maxlength="6"
                        :tabindex="1"
                        v-model="form.otp_code"
                        placeholder="Enter 6-digit code"
                        style="height: 49px"
                        class="text-center text-lg tracking-widest"
                    />
                    <InputError :message="form.errors.otp_code" />
                </div>

                <Button type="submit" class="btn-main mt-4 w-full" style="height: 49px" :tabindex="2" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    VERIFY & LOGIN
                </Button>

                <div class="text-center">
                    <button type="button" @click="resendOtp" :disabled="resendDisabled">
                        {{ resendText }}
                    </button>
                </div>
            </div>
        </form>
    </AuthBase>
</template>

<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    email: string;
}>();

const form = useForm({
    email: props.email,
    otp_code: '',
});

// Timer per resend
const resendTimer = ref(0);
const resendDisabled = computed(() => resendTimer.value > 0);
const resendText = computed(() => (resendTimer.value > 0 ? `Resend code in ${resendTimer.value}s` : 'Resend code'));

onMounted(() => {
    startResendTimer();
});

const startResendTimer = () => {
    resendTimer.value = 60;
    const interval = setInterval(() => {
        resendTimer.value--;
        if (resendTimer.value <= 0) {
            clearInterval(interval);
        }
    }, 1000);
};

const submit = () => {
    form.post(route('otp.verify'), {
        onFinish: () => form.reset('otp_code'),
        onSuccess: () => {},
    });
};

const resendOtp = () => {
    if (resendDisabled.value) return;

    const resendForm = useForm({
        email: props.email,
    });

    resendForm.post(route('otp.send'), {
        onSuccess: () => {
            startResendTimer();
        },
    });
};
</script>
