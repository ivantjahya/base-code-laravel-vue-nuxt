import type { AxiosResponse } from 'axios'

type ToastData = {
    type?: 'success' | 'info' | 'warn' | 'error' | 'secondary' | 'contrast' | undefined
    title?: string
    description?: string
    color?: 'error' | 'primary' | 'secondary' | 'success' | 'info' | 'warning' | 'neutral'
    duration?: number
    icon?: string
    response?: AxiosResponse | any
}

export const useCmpToast = () => {
    const toast = useToast()
    const durationDefault = 5000

    const toastDisplay = (data: ToastData) => {
        if (data.type === 'error') {
            const err = data.response

            if (typeof err === 'undefined') {
                toast.add({
                    title: 'Unknown Error',
                    description: 'Please contact administrator',
                    color: 'error',
                    duration: data.duration ?? durationDefault,
                    icon: data.icon ?? 'i-heroicons-x-circle',
                })
            } else {
                const status = err.response.status;

                if (status === 500) {
                    toast.add({
                        title: 'Server Error',
                        description: 'Please contact administrator',
                        color: 'error',
                        duration: data.duration ?? durationDefault,
                        icon: data.icon ?? 'i-heroicons-x-circle',
                    })
                } else if (status === 401) {
                    toast.add({
                        title: 'Unauthorized',
                        description: 'Action not authorized.',
                        color: 'error',
                        duration: data.duration ?? durationDefault,
                        icon: data.icon ?? 'i-heroicons-x-circle',
                    })
                } else if (status === 403) {
                    toast.add({
                        title: 'Forbidden',
                        description: 'Access denied.',
                        color: 'error',
                        duration: data.duration ?? durationDefault,
                        icon: data.icon ?? 'i-heroicons-x-circle',
                    })
                } else if (status === 404) {
                    toast.add({
                        title: 'Not Found',
                        description: 'Resource not found.',
                        color: 'error',
                        duration: data.duration ?? durationDefault,
                        icon: data.icon ?? 'i-heroicons-x-circle',
                    })
                } else if (err.response.data?.errors) {
                    Object.values(err.response.data.errors).forEach((v: any) => {
                        toast.add({
                            title: err.response.data.message,
                            description: v.toString(),
                            color: 'error',
                            duration: data.duration ?? durationDefault,
                            icon: data.icon ?? 'i-heroicons-x-circle',
                        })
                    })
                } else {
                    toast.add({
                        title: 'Unknown Error',
                        description: 'Please contact administrator, status code: ' + err.response.status,
                        color: 'error',
                        duration: data.duration ?? durationDefault,
                        icon: data.icon ?? 'i-heroicons-x-circle',
                    })
                }
            }
        } else {
            toast.add({
                title: data.title,
                description: data.description,
                color: data.color ?? 'primary',
                duration: data.duration ?? durationDefault,
                icon: data.icon ?? '',
            })
        }
    }

    return { toastDisplay }
}