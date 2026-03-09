import api from "./api"

export const login = async (login, password) => {
  const response = await api.post("/login", {
    login,
    password
  })

  return response.data
}

export const logout = async () => {
  const res = await api.post("/logout")
  return res.data
}