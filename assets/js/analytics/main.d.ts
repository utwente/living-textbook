declare module 'fos-routing' {
    export default interface Routing {
        generate(route: string, params?: { [key: string]: string }): string
    }
}
