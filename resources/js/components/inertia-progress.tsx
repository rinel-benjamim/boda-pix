import { router } from '@inertiajs/react'
import { useEffect, useState } from 'react'
import { Loader2 } from 'lucide-react'

export function InertiaProgress() {
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    const startHandler = () => setLoading(true)
    const finishHandler = () => setLoading(false)

    router.on('start', startHandler)
    router.on('finish', finishHandler)

    return () => {}
  }, [])

  if (!loading) return null

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-background/80 backdrop-blur-sm">
      <Loader2 className="h-8 w-8 animate-spin text-primary" />
    </div>
  )
}
