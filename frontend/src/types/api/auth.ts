declare namespace Api.Auth {
  interface LoginParams {
    account: string
    password: string
  }

  interface LoginResponse {
    token: string
    refreshToken: string
  }

  interface UserInfo {
    buttons: string[]
    roles: string[]
    userId: number
    userName: string
    email: string
    avatar?: string
  }
}
